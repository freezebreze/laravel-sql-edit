<script setup lang="ts">
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage, router } from '@inertiajs/vue3';
const page = usePage();
const showingNavigationDropdown = ref(false);
const navigation = computed(() => [
    {
        label: 'SQL 查询',
        icon: 'i-lucide-database',
        active: route().current('dev'),
        onSelect: () => router.visit(route('dev')),
    },
]);

const userMenu = computed(() => [
    [
        {
            label: '退出登录',
            icon: 'i-lucide-log-out',
            onSelect: () => router.post(route('logout')),
        },
    ],
]);
</script>

<template>
    <div class="min-h-screen bg-default">
        <UHeader>
            <UNavigationMenu :items="navigation" />

            <template #right>
                <UDropdownMenu :items="userMenu">
                    <UButton
                        color="neutral"
                        variant="ghost"
                        trailing-icon="i-lucide-chevron-down"
                    >
                        {{ $page.props.auth.user.name }}
                    </UButton>
                </UDropdownMenu>
            </template>

            <template #body>
                <UNavigationMenu :items="navigation" orientation="vertical" />
            </template>
        </UHeader>

        <UMain>
            <header v-if="$slots.header" class="border-b border-default">
                <UContainer class="py-6">
                    <slot name="header" />
                </UContainer>
            </header>

            <UContainer class="py-8">
                <slot />
            </UContainer>
        </UMain>
    </div>
</template>
