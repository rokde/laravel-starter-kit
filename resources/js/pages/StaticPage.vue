<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    title: string;
    content: string;
}

const props = defineProps<Props>();
</script>

<template>
    <Head :title="props.title">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]">
        <header class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    {{ $t('Dashboard') }}
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        {{ $t('Log in') }}
                    </Link>
                    <Link
                        :href="route('register')"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        {{ $t('Sign up') }}
                    </Link>
                </template>
            </nav>
        </header>
        <div class="flex w-full justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0">
            <main class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row">
                <div class="prose dark:prose-invert mt-6 w-full overflow-hidden sm:max-w-2xl dark:bg-gray-800" v-html="props.content" />
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
        <footer class="mt-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
            <div class="flex items-center justify-between">
                <div class="text-center text-sm text-gray-500 sm:text-left dark:text-gray-400">
                    <div class="flex items-center gap-4">
                        <a
                            href="/"
                            class="group inline-flex items-center hover:text-gray-700 focus:rounded-sm focus:outline-2 focus:outline-indigo-500 dark:hover:text-white"
                        >
                            Made with
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                class="mx-1 -mt-px h-5 w-5 stroke-gray-400 group-hover:fill-red-500 group-hover:stroke-red-600 dark:stroke-gray-600 dark:group-hover:fill-red-700 dark:group-hover:stroke-red-800"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"
                                />
                            </svg>
                            in Berlin
                        </a>
                    </div>
                </div>
                <nav class="ml-4 text-center text-sm text-gray-500 sm:ml-0 sm:text-right dark:text-gray-400">
                    <div class="flex flex-nowrap space-x-4">
                        <Link
                            href="/imprint"
                            class="hover:text-gray-700 focus:rounded-sm focus:outline-2 focus:outline-indigo-500 dark:hover:text-white"
                            >{{ $t('Imprint') }}</Link
                        >
                        <Link
                            href="/policy"
                            class="hover:text-gray-700 focus:rounded-sm focus:outline-2 focus:outline-indigo-500 dark:hover:text-white"
                            >{{ $t('Policy') }}</Link
                        >
                        <Link
                            href="/terms"
                            class="hover:text-gray-700 focus:rounded-sm focus:outline-2 focus:outline-indigo-500 dark:hover:text-white"
                            >{{ $t('Terms') }}</Link
                        >
                    </div>
                </nav>
            </div>
        </footer>
    </div>
</template>
