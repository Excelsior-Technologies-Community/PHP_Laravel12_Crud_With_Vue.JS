<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $categories = Category::all();
        $statuses = ['published', 'draft', 'archived'];
        $manager = new ImageManager(new Driver());
        $webpEncoder = new WebpEncoder(85);

        $titles = [
            'Getting Started with Laravel 12',
            'Vue 3 Composition API Tips',
            'Building Modern SPAs with Inertia.js',
            'Tailwind CSS Best Practices',
            'Mastering Laravel Eloquent',
            'Vue Router Deep Dive',
            'Laravel Breeze Authentication',
            'CSS Grid vs Flexbox',
            'REST API Design Principles',
            'Database Optimization Strategies',
            'Laravel Queues Explained',
            'Vue 3 Reactivity System',
            'Modern PHP Patterns',
            'Testing Laravel Applications',
            'Web Performance Optimization',
            'Laravel Middleware Guide',
            'Vue Component Design Patterns',
            'API Authentication with Sanctum',
            'Laravel Events and Listeners',
            'Building Scalable Applications',
        ];

        $bodies = [
            "Laravel 12 brings exciting new features and improvements to the framework. With better performance, enhanced developer experience, and modern PHP support, it's the perfect choice for building robust web applications. In this post, we'll explore the key features and how to leverage them effectively.\n\nWhether you're building a small blog or a large enterprise application, Laravel provides the tools you need to succeed. From the elegant syntax to the powerful ecosystem, every aspect of Laravel is designed with developer happiness in mind.",

            "Vue 3's Composition API revolutionizes how we write Vue components. By providing better logic reuse, improved TypeScript support, and more flexible code organization, it enables developers to build complex applications with cleaner, more maintainable code.\n\nIn this comprehensive guide, we'll explore the Composition API's core concepts, including ref(), reactive(), computed(), and watch(). You'll learn when to use each and how to structure your components for maximum flexibility.",

            "Inertia.js bridges the gap between server-side routing and client-side SPAs. It allows you to build modern single-page applications using your existing server-side framework without building an API. This approach combines the best of both worlds.\n\nWith Inertia, you get the productivity of server-side frameworks like Laravel with the interactivity of client-side frameworks like Vue. No more separate frontend and backend repositories, no more complex API authentication flows.",

            "Tailwind CSS has transformed how we approach CSS in modern web development. By providing utility-first classes, it enables rapid UI development while maintaining consistency across your application. However, mastering Tailwind requires understanding its design principles.\n\nIn this post, we'll cover best practices for organizing your styles, creating reusable components, and optimizing your CSS bundle size. You'll learn how to leverage Tailwind's configuration to create a design system that scales.",

            "Eloquent ORM is one of Laravel's most powerful features. It provides an elegant, expressive interface for working with your database. From basic CRUD operations to complex relationships and advanced query building, Eloquent simplifies database interactions.\n\nMaster Eloquent by understanding relationships, accessors, mutators, scopes, and eager loading. These concepts will help you write efficient, maintainable database code that leverages Laravel's full potential.",

            "Vue Router is essential for building single-page applications with Vue.js. It provides navigation guards, route parameters, nested routes, and transition effects. Understanding Vue Router deeply enables you to create complex, navigable applications.\n\nLearn about dynamic routing, navigation guards, route meta fields, and lazy loading routes. These advanced features help you build production-ready Vue applications with excellent user experience.",

            "Laravel Breeze provides a minimal starting point for authentication in Laravel applications. It includes login, registration, password reset, and profile management. Built with Tailwind CSS, it's easy to customize and extend.\n\nSet up Breeze in your Laravel project and customize the authentication flow to match your application's needs. We'll cover email verification, password confirmation, and session management.",

            "CSS Grid and Flexbox are two powerful layout systems in CSS. Understanding when to use each is crucial for building responsive designs. Flexbox excels at one-dimensional layouts, while Grid is designed for two-dimensional layouts.\n\nMaster both systems and learn how to combine them effectively. Real-world examples will demonstrate when to choose Grid over Flexbox and vice versa, helping you create layouts that are both flexible and maintainable.",

            "Designing RESTful APIs requires understanding HTTP methods, status codes, and resource naming conventions. A well-designed API is intuitive, consistent, and easy to consume. Follow industry best practices to create APIs that developers love to use.\n\nLearn about versioning, authentication, rate limiting, pagination, error handling, and documentation. These aspects ensure your API is production-ready and scalable.",

            "Database performance is critical for application scalability. Slow queries can bottleneck your entire application. Learn optimization techniques including indexing, query optimization, connection pooling, and caching strategies.\n\nDiscover how to identify slow queries, use database profiling tools, and implement effective caching layers. These optimizations can dramatically improve your application's response times.",

            "Laravel Queues allow you to defer time-consuming tasks, improving application responsiveness. By processing tasks asynchronously, you provide a better user experience while handling intensive operations efficiently.\n\nExplore queue drivers, job chaining, rate limiting, and failure handling. Learn to implement queues for email sending, image processing, and other background operations in your Laravel applications.",

            "Vue 3's reactivity system is built on Proxy objects, providing powerful reactive capabilities. Understanding how reactivity works internally helps you write more efficient Vue code and debug reactivity issues effectively.\n\nLearn about ref(), reactive(), computed properties, and watch effects. Understand when reactivity breaks down and how to work around limitations. This knowledge is essential for building complex Vue applications.",

            "Modern PHP development embraces type safety, immutability, and dependency injection. Design patterns like Repository, Service, and Factory help organize code and improve testability. Laravel embodies many of these patterns.\n\nApply modern PHP practices to write cleaner, more maintainable code. Learn about SOLID principles, value objects, and how Laravel's architecture supports these concepts out of the box.",

            "Testing is crucial for maintaining code quality. Laravel provides robust testing tools including PHPUnit integration, HTTP tests, database assertions, and browser testing with Dusk. Comprehensive tests catch bugs before they reach production.\n\nLearn to write unit tests, feature tests, and browser tests. Mock dependencies, test database interactions, and ensure your application works correctly across different scenarios.",

            "Web performance directly impacts user experience and SEO. Fast-loading pages retain users and rank better in search results. Optimize assets, minimize HTTP requests, implement lazy loading, and use caching effectively.\n\nMeasure performance with tools like Lighthouse and WebPageTest. Implement code splitting, image optimization, and CDN strategies to achieve excellent Core Web Vitals scores.",

            "Laravel middleware provides a convenient mechanism for filtering HTTP requests. Use middleware for authentication, logging, CORS, and other cross-cutting concerns. Understanding middleware enables powerful request/response manipulation.\n\nCreate custom middleware, register global and route-specific middleware, and understand middleware priority. Learn to build middleware that handles complex authorization and data transformation logic.",

            "Vue component design patterns promote reusability and maintainability. Patterns like renderless components, compound components, and dependency injection help organize complex UIs. Understanding these patterns elevates your Vue development skills.\n\nLearn to create flexible, composable components that handle various use cases. Master props, slots, provide/inject, and composition functions to build scalable component libraries.",

            "Laravel Sanctum provides simple API authentication. It supports SPA authentication and mobile app token authentication. Sanctum's lightweight approach makes it ideal for securing APIs without the complexity of OAuth.\n\nImplement token-based authentication, configure token abilities, and secure your API routes. Learn to handle authentication for SPAs and mobile applications effectively.",

            "Laravel Events and Listeners implement the observer pattern, decoupling application components. Events signal that something happened, while listeners respond to those events. This pattern promotes loose coupling and single responsibility.\n\nUse events for logging, notifications, cache invalidation, and other side effects. Learn to queue listeners, create subscribed events, and organize event-driven architecture in your Laravel applications.",

            "Building scalable applications requires careful architecture planning. Consider database design, caching strategies, queue systems, and microservices. Laravel provides tools for building applications that grow with your user base.\n\nPlan for scale from the beginning. Learn about database sharding, read replicas, horizontal scaling, and cloud deployment strategies. Build applications that handle growth gracefully.",
        ];

        foreach ($titles as $index => $title) {
            $status = $statuses[$index % 3];
            $category = $categories[$index % $categories->count()];
            $uniqueSlug = str($title)->slug() . '-' . time() . '-' . ($index + 1);

            $post = Post::create([
                'user_id' => $user->id,
                'title' => $title,
                'slug' => $uniqueSlug,
                'body' => $bodies[$index],
                'excerpt' => substr(strip_tags($bodies[$index]), 0, 150) . '...',
                'category_id' => $category->id,
                'status' => $status,
            ]);

            try {
                $imageUrl = "https://picsum.photos/seed/{$post->id}/1200/630";
                $imageContent = file_get_contents($imageUrl);

                if ($imageContent) {
                    $image = $manager->decode($imageContent)
                        ->orient()
                        ->scale(1200, 630, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })
                        ->encode($webpEncoder);

                    $path = 'posts/' . uniqid() . '.webp';
                    Storage::disk('public')->put($path, $image->toString());

                    $post->update(['featured_image' => $path]);
                }
            } catch (\Exception $e) {
                echo "Failed to download image for post {$post->id}: " . $e->getMessage() . PHP_EOL;
            }

            echo "Created post: {$title}" . PHP_EOL;
        }

        echo PHP_EOL . 'Total posts created: ' . Post::count() . PHP_EOL;
    }
}
