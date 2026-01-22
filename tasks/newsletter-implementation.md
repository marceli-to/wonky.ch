# Newsletter Module Implementation Plan

## Overview

Newsletter module for Filament with frontend subscription form. Newsletters contain multiple articles (1:n), each with title, text, and images.

---

## 1. Database Schema

### Migration: `create_subscribers_table`

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | primary key |
| uuid | uuid | unique identifier |
| email | string | unique |
| name | string | nullable |
| subscribed_at | timestamp | |
| unsubscribed_at | timestamp | nullable |
| token | string | for unsubscribe link |
| timestamps | | created_at, updated_at |

### Migration: `create_newsletters_table`

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | primary key |
| uuid | uuid | unique identifier |
| subject | string | email subject |
| preheader | string | nullable, email preview text |
| status | enum | draft, scheduled, sent |
| scheduled_at | timestamp | nullable |
| sent_at | timestamp | nullable |
| timestamps | | created_at, updated_at |

### Migration: `create_newsletter_articles_table`

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | primary key |
| newsletter_id | foreignId | constrained, cascadeOnDelete |
| title | string | |
| text | longtext | |
| order | integer | default 0 |
| timestamps | | created_at, updated_at |

### Images

Use existing polymorphic `images` table with `imageable_type = NewsletterArticle`

---

## 2. Models

### Subscriber (`app/Models/Subscriber.php`)

```php
- Fillable: uuid, email, name, subscribed_at, unsubscribed_at, token
- Casts: subscribed_at (datetime), unsubscribed_at (datetime)
- Boot: auto-generate uuid and token on creating
- Accessor: isActive (check if unsubscribed_at is null)
```

### Newsletter (`app/Models/Newsletter.php`)

```php
- Fillable: uuid, subject, preheader, status, scheduled_at, sent_at
- Casts: status (NewsletterStatus enum), scheduled_at (datetime), sent_at (datetime)
- Relationships:
  - HasMany: articles (NewsletterArticle)
- Boot: auto-generate uuid on creating
```

### NewsletterArticle (`app/Models/NewsletterArticle.php`)

```php
- Fillable: newsletter_id, title, text, order
- Relationships:
  - BelongsTo: newsletter
  - MorphMany: images (same pattern as Product model)
```

### NewsletterStatus Enum (`app/Enums/NewsletterStatus.php`)

```php
enum NewsletterStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Sent = 'sent';

    public function label(): string
    {
        return match($this) {
            self::Draft => 'Entwurf',
            self::Scheduled => 'Geplant',
            self::Sent => 'Gesendet',
        };
    }
}
```

---

## 3. Filament Resources

### SubscriberResource

**Location:** `app/Filament/Admin/Resources/Subscribers/`

```
├── SubscriberResource.php
├── Pages/
│   ├── ListSubscribers.php
│   ├── CreateSubscriber.php
│   └── EditSubscriber.php
├── Schemas/
│   └── SubscriberForm.php
└── Tables/
    └── SubscribersTable.php
```

**Form Fields:**
- email (required, email validation, unique)
- name (optional)
- subscribed_at (datetime)
- Toggle for active/unsubscribed status

**Table Columns:**
- email
- name
- subscribed_at
- status badge (active/unsubscribed)

**Table Filters:**
- Active subscribers only
- Unsubscribed

**Bulk Actions:**
- Delete selected

---

### NewsletterResource

**Location:** `app/Filament/Admin/Resources/Newsletters/`

```
├── NewsletterResource.php
├── Pages/
│   ├── ListNewsletters.php
│   ├── CreateNewsletter.php
│   └── EditNewsletter.php
├── Schemas/
│   └── NewsletterForm.php
└── Tables/
    └── NewslettersTable.php
```

**Form Fields:**
- subject (required)
- preheader (optional, helper text explaining purpose)
- status (Select: draft/scheduled/sent)
- scheduled_at (datetime, visible only when status = scheduled)

**Articles Repeater:**
```php
Repeater::make('articles')
    ->relationship('articles')
    ->orderColumn('order')
    ->reorderable()
    ->collapsible()
    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Artikel')
    ->schema([
        TextInput::make('title')
            ->label('Titel')
            ->required(),

        Textarea::make('text')  // or RichEditor
            ->label('Text')
            ->rows(6)
            ->required(),

        Repeater::make('images')
            ->relationship('images')
            ->schema([
                FileUpload::make('file_path')
                    ->image()
                    ->directory('newsletter')
                    ->disk('public'),
                TextInput::make('caption')
                    ->label('Bildunterschrift'),
            ])
    ])
```

**Table Columns:**
- subject
- status (badge with colors)
- articles_count
- sent_at
- created_at

---

## 4. Send Preview Action

Add to `EditNewsletter.php` page as header action:

```php
use Filament\Actions\Action;

protected function getHeaderActions(): array
{
    return [
        Action::make('sendPreview')
            ->label('Vorschau senden')
            ->icon('heroicon-o-paper-airplane')
            ->color('gray')
            ->form([
                TextInput::make('preview_email')
                    ->label('E-Mail Adresse')
                    ->email()
                    ->required()
                    ->default(auth()->user()->email)
            ])
            ->action(function (array $data) {
                $newsletter = $this->getRecord();

                Mail::to($data['preview_email'])
                    ->send(new NewsletterMail($newsletter, isPreview: true));

                Notification::make()
                    ->title('Vorschau gesendet')
                    ->success()
                    ->send();
            }),

        // ... other actions
    ];
}
```

---

## 5. Frontend Subscription

### Routes (`routes/web.php`)

```php
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->name('newsletter.subscribe');

Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])
    ->name('newsletter.unsubscribe');
```

### Controller (`app/Http/Controllers/NewsletterController.php`)

```php
class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:subscribers,email',
            'name' => 'nullable|string|max:255',
        ]);

        Subscriber::create([
            'email' => $validated['email'],
            'name' => $validated['name'] ?? null,
            'subscribed_at' => now(),
            'token' => Str::random(32),
        ]);

        return back()->with('newsletter_success', true);
    }

    public function unsubscribe(string $token)
    {
        $subscriber = Subscriber::where('token', $token)->firstOrFail();

        $subscriber->update([
            'unsubscribed_at' => now(),
        ]);

        return view('newsletter.unsubscribed');
    }
}
```

### Livewire Component (`app/Livewire/NewsletterSignup.php`)

```php
class NewsletterSignup extends Component
{
    public string $email = '';
    public string $name = '';
    public bool $success = false;
    public ?string $error = null;

    protected $rules = [
        'email' => 'required|email|unique:subscribers,email',
        'name' => 'nullable|string|max:255',
    ];

    public function subscribe()
    {
        $this->validate();

        try {
            Subscriber::create([
                'email' => $this->email,
                'name' => $this->name ?: null,
                'subscribed_at' => now(),
                'token' => Str::random(32),
            ]);

            $this->success = true;
            $this->reset(['email', 'name']);
        } catch (\Exception $e) {
            $this->error = 'Ein Fehler ist aufgetreten.';
        }
    }

    public function render()
    {
        return view('livewire.newsletter-signup');
    }
}
```

### Blade View (`resources/views/livewire/newsletter-signup.blade.php`)

```blade
<div>
    @if($success)
        <div class="text-green-600">
            Vielen Dank für Ihre Anmeldung!
        </div>
    @else
        <form wire:submit="subscribe" class="space-y-4">
            @honeypot

            <div>
                <input
                    type="email"
                    wire:model="email"
                    placeholder="E-Mail Adresse"
                    class="..."
                    required
                >
                @error('email')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <input
                    type="text"
                    wire:model="name"
                    placeholder="Name (optional)"
                    class="..."
                >
            </div>

            <button type="submit" class="...">
                Anmelden
            </button>
        </form>
    @endif
</div>
```

---

## 6. Email Templates

### Newsletter Mailable (`app/Mail/NewsletterMail.php`)

```php
class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Newsletter $newsletter,
        public bool $isPreview = false,
        public ?string $unsubscribeToken = null
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->isPreview
            ? '[VORSCHAU] ' . $this->newsletter->subject
            : $this->newsletter->subject;

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter',
        );
    }
}
```

### Email Template (`resources/views/emails/newsletter.blade.php`)

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">

    @if($isPreview)
        <div style="background: #fef3c7; padding: 10px; text-align: center;">
            Dies ist eine Vorschau
        </div>
    @endif

    @foreach($newsletter->articles as $article)
        <div style="margin-bottom: 30px;">
            <h2>{{ $article->title }}</h2>

            @foreach($article->images as $image)
                <img src="{{ asset('storage/' . $image->file_path) }}"
                     alt="{{ $image->caption }}"
                     style="max-width: 100%; height: auto;">
            @endforeach

            <div>{!! nl2br(e($article->text)) !!}</div>
        </div>
    @endforeach

    @if($unsubscribeToken)
        <hr>
        <p style="font-size: 12px; color: #666;">
            <a href="{{ route('newsletter.unsubscribe', $unsubscribeToken) }}">
                Newsletter abbestellen
            </a>
        </p>
    @endif

</body>
</html>
```

---

## 7. Implementation Order

1. **Database Layer**
   - Create migrations
   - Run migrations
   - Create NewsletterStatus enum

2. **Models**
   - Create Subscriber model
   - Create Newsletter model
   - Create NewsletterArticle model

3. **Filament Backend**
   - Create SubscriberResource (full CRUD)
   - Create NewsletterResource with articles repeater
   - Add "Send Preview" action

4. **Email System**
   - Create NewsletterMail mailable
   - Create email blade template

5. **Frontend**
   - Create NewsletterController
   - Create Livewire NewsletterSignup component
   - Add routes
   - Create unsubscribe confirmation view

6. **Testing**
   - Test subscriber signup flow
   - Test newsletter creation with articles
   - Test preview sending
   - Test unsubscribe flow

---

## 8. Files to Create

```
database/migrations/
  xxxx_xx_xx_xxxxxx_create_subscribers_table.php
  xxxx_xx_xx_xxxxxx_create_newsletters_table.php
  xxxx_xx_xx_xxxxxx_create_newsletter_articles_table.php

app/Enums/
  NewsletterStatus.php

app/Models/
  Subscriber.php
  Newsletter.php
  NewsletterArticle.php

app/Filament/Admin/Resources/Subscribers/
  SubscriberResource.php
  Pages/
    ListSubscribers.php
    CreateSubscriber.php
    EditSubscriber.php
  Schemas/
    SubscriberForm.php
  Tables/
    SubscribersTable.php

app/Filament/Admin/Resources/Newsletters/
  NewsletterResource.php
  Pages/
    ListNewsletters.php
    CreateNewsletter.php
    EditNewsletter.php
  Schemas/
    NewsletterForm.php
  Tables/
    NewslettersTable.php

app/Http/Controllers/
  NewsletterController.php

app/Livewire/
  NewsletterSignup.php

app/Mail/
  NewsletterMail.php

resources/views/
  livewire/newsletter-signup.blade.php
  emails/newsletter.blade.php
  newsletter/unsubscribed.blade.php
```

---

## 9. Configuration Notes

- Use existing `spatie/laravel-honeypot` for spam protection on signup form
- Newsletter images stored in `storage/app/public/newsletter/`
- Consider adding queue for sending newsletters to multiple subscribers
- German labels used throughout (Swiss market)
