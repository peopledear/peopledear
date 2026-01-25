# File Storage

## Overview

PeopleDear uses S3-compatible storage via MinIO (locally) or AWS S3 (production) for file uploads. Two buckets are configured:

- **Private**: Sensitive files (documents, receipts, internal data)
- **Public**: Publicly accessible assets (avatars, logos)

## The Disk Enum

Use the `App\Enums\Disk` enum for type-safe disk references:

```php
use App\Enums\Disk;

// Get a Storage instance directly
Disk::S3Private->storage()->put('documents/file.pdf', $contents);
Disk::S3Public->storage()->put('avatars/user-123.jpg', $image);

// Get public URL for assets
$url = Disk::S3Public->storage()->url('avatars/user-123.jpg');
```

### Available Disks

| Enum Case | Disk Name | Use Case |
|-----------|-----------|----------|
| `Disk::Local` | `local` | Local private files (development) |
| `Disk::Public` | `public` | Local public files with symlink |
| `Disk::S3Private` | `s3-private` | Private S3/MinIO bucket |
| `Disk::S3Public` | `s3-public` | Public S3/MinIO bucket |
| `Disk::Tenanted` | `tenanted` | Sprout tenant-scoped storage |

## Usage Examples

### Uploading Files

```php
use App\Enums\Disk;

// Private document upload
Disk::S3Private->storage()->put(
    "employees/{$employee->id}/contracts/contract.pdf",
    $request->file('contract')->get()
);

// Public avatar upload
$path = Disk::S3Public->storage()->putFile(
    "avatars",
    $request->file('avatar')
);
```

### Generating URLs

```php
use App\Enums\Disk;

// Public files - direct URL
$avatarUrl = Disk::S3Public->storage()->url("avatars/{$filename}");

// Private files - temporary signed URL
$documentUrl = Disk::S3Private->storage()->temporaryUrl(
    "documents/{$filename}",
    now()->addMinutes(30)
);
```

### Checking & Deleting Files

```php
use App\Enums\Disk;

// Check existence
if (Disk::S3Private->storage()->exists($path)) {
    // Delete file
    Disk::S3Private->storage()->delete($path);
}

// Delete multiple files
Disk::S3Public->storage()->delete([
    'avatars/old-1.jpg',
    'avatars/old-2.jpg',
]);
```

## Local Development (MinIO)

MinIO is provided by Laravel Herd and runs at `https://minio.herd.test`.

### Setup

1. Ensure MinIO is running in Herd
2. Access the dashboard at `https://minio.herd.test`
3. Create two buckets: `peopledear-private` and `peopledear-public`
4. Set the `peopledear-public` bucket access policy to allow public reads

### Environment Variables

```env
# MinIO / S3 Configuration
AWS_ACCESS_KEY_ID=herd
AWS_SECRET_ACCESS_KEY=secretkey
AWS_DEFAULT_REGION=us-east-1
AWS_USE_PATH_STYLE_ENDPOINT=true
AWS_ENDPOINT=https://minio.herd.test

# Private Bucket
AWS_PRIVATE_BUCKET=peopledear-private

# Public Bucket
AWS_PUBLIC_BUCKET=peopledear-public
AWS_PUBLIC_URL=https://minio.herd.test/peopledear-public
```

## Production (AWS S3)

For production, update environment variables to use AWS S3:

```env
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=eu-west-1
AWS_USE_PATH_STYLE_ENDPOINT=false
AWS_ENDPOINT=

AWS_PRIVATE_BUCKET=peopledear-private
AWS_PUBLIC_BUCKET=peopledear-public
AWS_PUBLIC_URL=https://peopledear-public.s3.eu-west-1.amazonaws.com
```

## Best Practices

### Always Use the Enum

```php
// GOOD: Type-safe, refactorable
Disk::S3Private->storage()->put($path, $content);

// BAD: Magic string, error-prone
Storage::disk('s3-private')->put($path, $content);
```

### Organize Files by Entity

```php
// GOOD: Organized path structure
"employees/{$employee->id}/documents/{$filename}"
"organizations/{$org->id}/logos/{$filename}"

// BAD: Flat structure
"documents/{$filename}"
```

### Use Temporary URLs for Private Files

```php
// GOOD: Time-limited access
$url = Disk::S3Private->storage()->temporaryUrl($path, now()->addHour());

// BAD: Exposing private bucket paths
$url = Disk::S3Private->storage()->url($path);
```
