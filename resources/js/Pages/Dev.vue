<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

const sql = ref('SELECT * FROM users LIMIT 10;');
const executing = ref(false);
const error = ref('');

const data = ref([]);

const executeSql = async () => {
    if (!sql.value.trim() || executing.value) return;

    executing.value = true;
    error.value = '';

    try {
        const response = await axios.post(route('dev.execute'), {
            sql: sql.value,
        });

        data.value = response.data.data;
    } catch (exception: any) {
        error.value =
            exception.response?.data?.message ?? 'SQL 执行失败，请稍后重试。';
    } finally {
        executing.value = false;
    }
};
</script>

<template>
    <Head title="Sql Query" />

    <AuthenticatedLayout>
        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="mb-4 space-y-3 bg-white p-4 shadow-sm sm:rounded-lg"
                >
                    <UTextarea
                        v-model="sql"
                        :rows="8"
                        autoresize
                        placeholder="请输入 SQL 查询语句..."
                        class="w-full"
                    />

                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm text-gray-500"></p>

                        <UButton
                            icon="i-lucide-play"
                            :loading="executing"
                            :disabled="!sql.trim()"
                            @click="executeSql"
                        >
                            Execute
                        </UButton>
                    </div>

                    <p v-if="error" class="text-sm text-red-600">
                        {{ error }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <UTable :data="data" class="flex-1" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
