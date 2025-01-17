<script setup>
import '../../../resources/css/welcome.css';
import { Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
    blogPosts: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <header>
        <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
            >
                Dashboard
            </Link>

            <template v-else>
                <Link :href="route('login')">
                    Log in
                </Link>

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                >
                    Register
                </Link>
            </template>
        </nav>
    </header>

    <div class="flex flex-col items-center bg-[#e1e0da] text-center min-h-screen">
        <div class="m-5">
            <img src="/img/tailgunner-logo-full.png" alt="Tailgunner Logo">
        </div>

        <div class="block py-2">
            <h2 class="text-xl font-semibold mb-4">
                Building Tailgunner:
                <a href="https://bendauphinee.com/writing/building-tailgunner/">
                    Read About The Process
                </a>
            </h2>

            <table class="post_table">
                <thead>
                    <tr>
                        <th>Post Title</th>
                        <th>Date Posted</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="post in blogPosts"
                        :key="post.url"
                        class="odd:bg-gray-50">
                        <td>
                            <a :href="post.url">
                                {{ post.title }}
                            </a>
                        </td>
                        <td>{{ post.date }}</td>
                    </tr>
                </tbody>
            </table>

            <h2 class="text-xl font-semibold mb-4">
                Github:
                <a href="https://github.com/bendauphinee/tailgunner">
                    See The Code
                </a>
            </h2>

            <a href="https://bendauphinee.com/writing/2024/12/22/initial-development-setup-building-tailgunner/">
                Initial Development Setup (Dec 22, 2024)
            </a>
        </div>
    </div>
    <footer class="py-16 text-center text-sm text-black dark:text-white/70">
        Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
    </footer>
</template>
