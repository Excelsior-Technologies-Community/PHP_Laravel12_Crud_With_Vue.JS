# PHP_Laravel12_Crud_With_Vue.JS

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel">
  <img src="https://img.shields.io/badge/Vue.js-3.x-42b883?style=for-the-badge&logo=vue.js">
  <img src="https://img.shields.io/badge/Inertia.js-SPA-blueviolet?style=for-the-badge">
  <img src="https://img.shields.io/badge/Breeze-Auth-success?style=for-the-badge">
</p>

---

##  Overview

This project demonstrates a **complete CRUD (Create, Read, Update, Delete)** system
using **Laravel 12**, **Laravel Breeze**, **Inertia.js**, and **Vue.js**.

It includes:
- Authentication (Login / Register)
- Dashboard
- Posts CRUD module
- Vue pages with Tailwind UI
- SPA-like experience (no page reload)

---

##  Features

- Laravel 12 backend
- Breeze authentication
- Vue 3 + Inertia frontend
- Full Posts CRUD
- Form validation
- Tailwind CSS UI
- Clean folder structure

---

##  Folder Structure

```
app/
├── Http/Controllers/PostController.php
├── Models/Post.php

database/
└── migrations/xxxx_create_posts_table.php

resources/
└── js/
    ├── Pages/
    │   └── Post/
    │       ├── Index.vue
    │       ├── Create.vue
    │       └── Edit.vue
    └── Layouts/AuthenticatedLayout.vue

routes/
└── web.php

.env
README.md
```

---

##  Step 1 — Install Laravel 12

```bash
composer create-project laravel/laravel laravel12-crud
```

---

##  Step 2 — Database Configuration

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=post
DB_USERNAME=root
DB_PASSWORD=
```

Create database **post** in phpMyAdmin or MySQL.

---

##  Step 3 — Install Laravel Breeze (Vue + Inertia)

```bash
composer require laravel/breeze --dev

php artisan breeze:install
```

Choose:
- Vue with Inertia
- Dark mode (optional)

Install dependencies and migrate:

```bash
npm install

php artisan migrate
```

At this stage:
- Login / Register
- Dashboard  
are ready.

---

##  Step 4 — Run Project (Initial Test)

```bash
npm run dev

php artisan serve
```

URLs:
```
http://localhost:8000
http://localhost:8000/login
```

---

##  Step 5 — Create Post Model & Migration

```bash
php artisan make:model Post -m
```

### Migration

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('body');
    $table->timestamps();
});
```

Run:
```bash
php artisan migrate
```

### Post Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // Allow mass assignment for these fields
    protected $fillable = ['title', 'body'];
}

```

---

##  Step 6 — Create PostController

```bash
php artisan make:controller PostController
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    // Show all posts
    public function index()
    {
        return Inertia::render('Post/Index', [
            'posts' => Post::latest()->get()
        ]);
    }

    // Show create form
    public function create()
    {
        return Inertia::render('Post/Create');
    }

    // Store post
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body'  => 'required',
        ]);

        Post::create($request->all());

        return redirect()->route('posts.index');
    }

    // Show edit form
    public function edit(Post $post)
    {
        return Inertia::render('Post/Edit', [
            'post' => $post
        ]);
    }

    // Update post
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required',
            'body'  => 'required',
        ]);

        $post->update($request->all());

        return redirect()->route('posts.index');
    }

    // Delete post
    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->back();
    }
}

```

---

##  Step 7 — Routes Configuration

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('posts', PostController::class);
});
```

---

##  Step 8 — Vue Pages (Main Code)

### 📄 Index.vue
```vue
<template>
  <AuthenticatedLayout>
    <Link href="/posts/create">Create New Post</Link>

    <table>
      <tr v-for="post in posts" :key="post.id">
        <td>{{ post.title }}</td>
        <td>
          <Link :href="`/posts/${post.id}/edit`">Edit</Link>
        </td>
      </tr>
    </table>
  </AuthenticatedLayout>
</template>
```

---

### 📄 Create.vue
```vue
<template>
  <AuthenticatedLayout>
    <form @submit.prevent="submit">
      <input v-model="form.title" />
      <textarea v-model="form.body"></textarea>
      <button>Save</button>
    </form>
  </AuthenticatedLayout>
</template>
```

---

### 📄 Edit.vue
```vue
<template>
  <AuthenticatedLayout>
    <form @submit.prevent="submit">
      <input v-model="form.title" />
      <textarea v-model="form.body"></textarea>
      <button>Update</button>
    </form>
  </AuthenticatedLayout>
</template>
```

---

##  Step 9 — Add Posts Menu

`resources/js/Layouts/AuthenticatedLayout.vue`

```vue
<NavLink 
  :href="route('posts.index')" 
  :active="route().current('posts.*')"
>
  Posts
</NavLink>
```

---

##  Step 10 — Final Run

```bash
npm run dev

php artisan serve
```

URLs:
```
http://localhost:8000/posts
```

---
POST INDEX PAGE:-

<img width="1699" height="613" alt="Screenshot 2025-12-16 141034" src="https://github.com/user-attachments/assets/ee21d071-7d46-46bd-9c84-38cf890f5fb2" />

CREATE POST PAGE:-

<img width="1719" height="695" alt="Screenshot 2025-12-16 144351" src="https://github.com/user-attachments/assets/a1c6cac8-2317-4c1e-90b0-e8be518ed78d" />

EDIT POST PAGE:-

<img width="1646" height="663" alt="Screenshot 2025-12-16 141047" src="https://github.com/user-attachments/assets/c3797e1d-6ec2-4e68-8ea0-efcede6cf7cf" />

DATABSE STORE(MY SQL):-

<img width="853" height="285" alt="Screenshot 2025-12-16 141853" src="https://github.com/user-attachments/assets/50caa007-6c9f-4ef3-abcb-00e8961bf1d6" />



