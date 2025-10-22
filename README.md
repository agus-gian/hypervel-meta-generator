# Hypervel Meta Generator

Hypervel Meta Generator adalah package powerful yang memungkinkan Anda dengan mudah melampirkan dan mengelola metadata untuk model Eloquent Anda tanpa memodifikasi tabel database utama mereka. Package ini menyediakan sistem key-value yang fleksibel dengan fitur deteksi tipe otomatis, casting, dan perintah artisan yang berguna untuk menyederhanakan instalasi dan maintenance.

> **🚀 Dibangun untuk Hypervel Framework** - Framework PHP high-performance dengan dukungan coroutine native berbasis Swoole.

---

## Daftar Isi

- [Fitur](#fitur)
- [Instalasi](#instalasi)
- [Penggunaan](#penggunaan)
  - [Melampirkan Metadata ke Model](#melampirkan-metadata-ke-model)
  - [Mengelola Metadata](#mengelola-metadata)
- [Artisan Commands](#artisan-commands)
  - [`make:metadata`](#makemetadata)
  - [`metadata:clean-orphaned`](#metadataclean-orphaned)
- [Konfigurasi](#konfigurasi)
- [Perbedaan dengan Laravel Version](#perbedaan-dengan-laravel-version)
- [License](#license)

---

## Fitur

✨ **Auto Type Detection** - Deteksi tipe data otomatis (string, integer, boolean, json, datetime, dll)
✨ **Type Casting** - Casting otomatis saat retrieve data
✨ **Query Scopes** - Query builder scopes untuk filter berdasarkan metadata
✨ **Artisan Commands** - Generate metadata system dan clean orphaned records
✨ **Coroutine Safe** - Dioptimasi untuk environment Swoole/Hypervel dengan coroutine support
✨ **Zero Table Modification** - Tidak perlu mengubah tabel database utama

---

## Instalasi

Ada dua cara untuk mengintegrasikan Hypervel Meta Generator ke dalam project Anda:

### 1. Via Packagist (Coming Soon)

```bash
composer require augustpermana/hypervel-meta-generator
```

Hypervel akan secara otomatis menemukan service provider melalui package discovery.

### 2. Menggunakan Local Repository

Jika package belum dipublish di Packagist, tambahkan sebagai local repository. Modifikasi `composer.json` project Anda:

```json
"repositories": [
    {
        "type": "path",
        "url": "./vendor/augustpermana/hypervel-meta-generator"
    }
],
"require": {
    "augustpermana/hypervel-meta-generator": "@dev"
}
```

Kemudian jalankan:

```bash
composer update augustpermana/hypervel-meta-generator
```

---

## Penggunaan

Hypervel Meta Generator memungkinkan Anda melampirkan metadata ke model tanpa memodifikasi tabel database asli.

### Melampirkan Metadata ke Model

1. **Generate Metadata Files:**

   Jalankan artisan command untuk setup metadata system untuk model yang ada. Contoh untuk model `Product`:

   ```bash
   php artisan make:metadata --model=Product
   ```

   Saat Anda menjalankan command ini, akan melakukan:
   - **Membuat Meta Model File:** Generate file baru (contoh: `ProductMeta.php`) di direktori `app/Models`
   - **Membuat Migration:** Generate migration untuk membuat tabel metadata (contoh: `product_meta`)

2. **Update Model Asli:**

   Anda harus secara manual update model asli (contoh: `Product.php`) untuk menyertakan trait `HasMetadata`:

   ```php
   <?php

   namespace App\Models;

   use App\Models\Model;
   use AugustPermana\HypervelMetaGenerator\Traits\HasMetadata;

   class Product extends Model
   {
       use HasMetadata;
       
       // ... model code lainnya
   }
   ```

3. **Jalankan Migration:**

   ```bash
   php artisan migrate
   ```

### Mengelola Metadata

Setelah setup, Anda dapat menggunakan berbagai method untuk manage metadata:

#### Set Single Meta

```php
$product = Product::find(1);
$product->setMeta('warranty_period', 24); // Auto-detected as integer
$product->setMeta('is_featured', true);   // Auto-detected as boolean
$product->setMeta('specifications', [     // Auto-detected as json
    'color' => 'black',
    'weight' => '1.5kg'
]);
```

#### Get Single Meta

```php
$warrantyPeriod = $product->getMeta('warranty_period'); // Returns: 24 (as integer)
$isFeatured = $product->getMeta('is_featured');         // Returns: true (as boolean)
$specs = $product->getMeta('specifications');           // Returns: array

// Dengan default value
$discount = $product->getMeta('discount', 0); // Returns: 0 jika tidak ada
```

#### Set Multiple Meta

```php
$product->setManyMeta([
    'brand' => 'Samsung',
    'warranty_period' => 24,
    'is_featured' => true,
    'release_date' => '2025-10-22'
]);
```

#### Sync Meta (Replace All)

```php
// Hapus semua metadata yang ada dan ganti dengan yang baru
$product->syncMeta([
    'brand' => 'Apple',
    'model' => 'iPhone 15',
    'price' => 999.99
]);
```

#### Check Meta Exists

```php
if ($product->hasMeta('warranty_period')) {
    // Metadata exists
}
```

#### Remove Meta

```php
$product->removeMeta('old_field');
```

#### Query dengan Meta Scope

```php
// Cari semua produk yang featured
$featuredProducts = Product::whereHasMeta('is_featured', '1')->get();

// Cari semua produk yang punya metadata 'warranty_period'
$productsWithWarranty = Product::whereHasMeta('warranty_period')->get();
```

---

## Artisan Commands

### `make:metadata`

Generate metadata system untuk model yang sudah ada.

```bash
php artisan make:metadata --model=Product
```

Command ini akan:
- Membuat model meta baru (`ProductMeta.php`)
- Membuat migration untuk tabel metadata

### `metadata:clean-orphaned`

Membersihkan orphaned metadata records (metadata yang parent recordnya sudah dihapus).

```bash
php artisan metadata:clean-orphaned --model=Product
```

**⚠️ PERINGATAN:** Command ini akan menghapus data dari database. Backup database Anda sebelum menjalankan command ini.

---

## Konfigurasi

Package ini tidak memerlukan konfigurasi khusus. Namun, Anda dapat customize:

### Custom Meta Model Location

Secara default, meta model akan dibuat di `App\Models`. Jika Anda ingin menggunakan lokasi berbeda, override method `getMetaModelClass()` di model Anda:

```php
protected function getMetaModelClass()
{
    return 'App\\CustomNamespace\\' . class_basename($this) . 'Meta';
}
```

### Supported Data Types

Package ini secara otomatis mendeteksi dan mendukung tipe data berikut:

- `string` - String pendek
- `text` - String panjang (> 255 chars)
- `longtext` - String sangat panjang (> 65535 chars)
- `integer` - Bilangan bulat
- `float` / `double` - Bilangan desimal
- `decimal` - Bilangan desimal presisi
- `boolean` - True/False
- `json` - Array atau Object
- `date` - Tanggal (Y-m-d)
- `time` - Waktu (H:i:s)
- `datetime` / `timestamp` - Tanggal dan waktu
- `binary` - Binary data (base64 encoded)

---

## Perbedaan dengan Laravel Version

Package ini adalah adaptasi dari `augustpermana/laravel-meta-generator` untuk Hypervel Framework dengan perubahan berikut:

| Fitur | Laravel Version | Hypervel Version |
|-------|----------------|------------------|
| **Framework** | Laravel 8+ | Hypervel 0.3+ |
| **Base Classes** | `Illuminate\*` | `Hypervel\*` |
| **PHP Version** | >= 7.3 | >= 8.2 |
| **Coroutine Support** | ❌ | ✅ |
| **Swoole Extension** | Optional | Required |
| **Performance** | Standard | High (Coroutine-based) |
| **Package Discovery** | `laravel` key | `hypervel` key |

---

## Performance Tips

### 1. Eager Loading Metadata

```php
// ❌ N+1 Problem
$products = Product::all();
foreach ($products as $product) {
    $brand = $product->getMeta('brand'); // Query di setiap loop
}

// ✅ Eager Loading
$products = Product::with('meta')->get();
foreach ($products as $product) {
    $brand = $product->getMeta('brand'); // Tidak ada query tambahan
}
```

### 2. Batch Operations

```php
// ❌ Multiple Queries
$product->setMeta('field1', 'value1');
$product->setMeta('field2', 'value2');
$product->setMeta('field3', 'value3');

// ✅ Single Batch
$product->setManyMeta([
    'field1' => 'value1',
    'field2' => 'value2',
    'field3' => 'value3',
]);
```

### 3. Indexing

Tabel metadata secara otomatis memiliki composite index pada `(foreign_key, key)` untuk query yang lebih cepat.

---

## Requirements

- **PHP:** >= 8.2
- **Hypervel Framework:** ^0.3
- **Swoole Extension:** >= 5.0
- **ext-json:** *

---

## Contributing

Kontribusi sangat diterima! Silakan buat pull request atau buka issue di repository GitHub.

---

## License

Package ini adalah open-source software yang dilisensikan di bawah [MIT license](LICENSE).

---

## Author

**Agus Permana**
- Email: agus.emailnya@gmail.com
- GitHub: [@agus-gian](https://github.com/agus-gian)

---

## Credits

Package ini terinspirasi dari kebutuhan untuk mengelola metadata dinamis tanpa mengubah struktur tabel database, dengan optimasi khusus untuk environment Hypervel yang high-performance.

---

**Happy Coding with Hypervel! 🚀**
