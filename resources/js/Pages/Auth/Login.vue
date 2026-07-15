<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { AuthFormField, FormSubmitEvent } from '@nuxt/ui';
import * as z from 'zod';

defineProps<{
    status?: string;
}>();

const fields: AuthFormField[] = [
    {
        name: 'email',
        type: 'email',
        label: '邮箱',
        placeholder: '请输入邮箱',
        autocomplete: 'username',
        required: true,
    },
    {
        name: 'password',
        type: 'password',
        label: '密码',
        placeholder: '请输入密码',
        autocomplete: 'current-password',
        required: true,
    },
    {
        name: 'remember',
        type: 'checkbox',
        label: '记住我',
    },
];

const schema = z.object({
    email: z.email('请输入有效的邮箱地址'),
    password: z.string().min(8, '密码至少需要 8 个字符'),
    remember: z.boolean().optional(),
});

type Schema = z.output<typeof schema>;

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit(event: FormSubmitEvent<Schema>) {
    form.email = event.data.email;
    form.password = event.data.password;
    form.remember = event.data.remember ?? false;

    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="登录" />

    <main
        class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 dark:bg-gray-950"
    >
        <div class="flex w-full max-w-md flex-col items-center gap-6">
            <Link href="/" aria-label="返回首页">
                <ApplicationLogo class="h-16 w-16 fill-current text-gray-500" />
            </Link>

            <UPageCard class="w-full">
                <UAuthForm
                    :schema="schema"
                    :fields="fields"
                    title="登录"
                    description="输入你的账户信息以继续"
                    :loading="form.processing"
                    :submit="{ label: '登录' }"
                    @submit="submit"
                >
                    <template
                        v-if="status || Object.keys(form.errors).length"
                        #validation
                    >
                        <UAlert
                            :color="status ? 'success' : 'error'"
                            :title="
                                status ||
                                form.errors.email ||
                                form.errors.password
                            "
                        />
                    </template>
                </UAuthForm>
            </UPageCard>
        </div>
    </main>
</template>
