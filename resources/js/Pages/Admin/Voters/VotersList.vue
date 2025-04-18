<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import QRCodeVue3 from 'qrcode.vue';

const props = defineProps({
    voters: {
        type: Object,
        default: () => ({ data: [] })
    }
});
</script>

<template>
    <Head title="Voters List" />
    <AuthenticatedLayout>
        <div class="py-5">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <Link :href="route('voters.create')">Import data</Link>
                        <h2 class="text-2xl font-semibold mb-4">Voters List</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">First Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Last Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Middle Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">UUID</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="!props.voters?.data?.length">
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No voters found
                                        </td>
                                    </tr>
                                    <tr v-for="voter in props.voters?.data ?? []" :key="voter?.id">
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ voter.first_name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ voter.last_name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ voter.middle_name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 flex flex-col justify-center items-center">
                                            <QRCodeVue3
                                                :value="voter.uuid"
                                                :size="100"
                                                level="H"
                                                class="border border-gray-200 rounded"
                                            />
                                            {{ voter.uuid }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 flex justify-center">
                            <nav class="inline-flex space-x-2">
                                <Link
                                    v-for="(link, index) in props.voters.links"
                                    :key="index"
                                    :href="link.url || ''"
                                    preserve-scroll
                                    preserve-state
                                    class="px-4 py-2 rounded-md text-sm"
                                    :class="{
                                        'bg-indigo-600 text-white': link.active,
                                        'bg-white text-gray-700 border': !link.active,
                                        'cursor-not-allowed opacity-50': !link.url
                                    }"
                                >
                                    <span v-html="link.label" />
                                </Link>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
