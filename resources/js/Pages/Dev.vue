<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { basicSetup } from 'codemirror';
import { sql as sqlLanguage } from '@codemirror/lang-sql';
import { EditorView, placeholder } from '@codemirror/view';

const DEFAULT_SQL = 'SELECT * FROM sql_log;';
const sql = ref(DEFAULT_SQL);
const executing = ref(false);
const error = ref('');

const data = ref<Record<string, unknown>[]>([]);
const page = ref(1);
const perPage = ref(10);
const total = ref(0);
const editorElement = ref<HTMLElement | null>(null);
let editorView: EditorView | null = null;

onMounted(() => {
    if (!editorElement.value) return;

    editorView = new EditorView({
        doc: sql.value,
        extensions: [
            basicSetup,
            sqlLanguage(),
            placeholder('请输入 SQL 查询语句...'),
            EditorView.lineWrapping,
            EditorView.updateListener.of((update) => {
                if (update.docChanged) {
                    sql.value = update.state.doc.toString();
                }
            }),
            EditorView.theme({
                '&': {
                    minHeight: '12rem',
                    fontSize: '0.875rem',
                },
                '.cm-scroller': {
                    minHeight: '12rem',
                    fontFamily:
                        'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace',
                },
                '.cm-content': {
                    padding: '0.75rem 0',
                },
                '&.cm-focused': {
                    outline: 'none',
                },
            }),
        ],
        parent: editorElement.value,
    });
});

onBeforeUnmount(() => {
    editorView?.destroy();
});

const loadPage = async (requestedPage: number) => {
    if (!sql.value.trim() || executing.value) return;

    executing.value = true;
    error.value = '';

    try {
        const response = await axios.post(route('dev.execute'), {
            sql: sql.value,
            page: requestedPage,
            per_page: perPage.value,
        });

        data.value = response.data.data;
        page.value = response.data.pagination.page;
        total.value = response.data.pagination.total;
    } catch (exception: any) {
        error.value =
            exception.response?.data?.message ?? 'SQL 执行失败，请稍后重试。';
    } finally {
        executing.value = false;
    }
};

const executeSql = () => loadPage(1);

const changePage = (newPage: number) => loadPage(newPage);

const resetInput = () => {
    if (executing.value) return;
    data.value = [];
    page.value = 1;
    total.value = 0;

    error.value = '';

    if (!editorView) {
        sql.value = DEFAULT_SQL;
        return;
    }

    editorView.dispatch({
        changes: {
            from: 0,
            to: editorView.state.doc.length,
            insert: DEFAULT_SQL,
        },
        selection: { anchor: DEFAULT_SQL.length },
    });
    editorView.focus();
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
                    <div
                        ref="editorElement"
                        class="overflow-hidden rounded-md border border-gray-300 bg-white focus-within:border-primary-500 focus-within:ring-1 focus-within:ring-primary-500"
                    ></div>

                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm text-gray-500"></p>
                        <div>
                            <UButton
                                color="neutral"
                                variant="outline"
                                icon="i-lucide-rotate-ccw"
                                :loading="executing"
                                :disabled="executing || sql === DEFAULT_SQL"
                                @click="resetInput"
                                class="mr-2"
                            >
                                Reset
                            </UButton>
                            <UButton
                                icon="i-lucide-play"
                                :loading="executing"
                                :disabled="!sql.trim()"
                                @click="executeSql"
                            >
                                Execute
                            </UButton>
                        </div>
                    </div>

                    <p v-if="error" class="text-sm text-red-600">
                        {{ error }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <UTable
                        :data="data"
                        :ui="{
                            base: 'table-fixed',
                            th: 'w-48 min-w-48 max-w-48 truncate',
                            td: 'w-48 min-w-48 max-w-48 truncate whitespace-nowrap',
                        }"
                        class="flex-1"
                    />
                </div>
                <div class="bg-white flex justify-center mt-4">
                    <UPagination
                        v-if="total > 0"
                        :page="page"
                        :total="total"
                        :items-per-page="perPage"
                        :disabled="executing"
                        @update:page="changePage"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
