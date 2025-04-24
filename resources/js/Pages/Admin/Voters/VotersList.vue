<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import QRCodeVue3 from 'qrcode.vue';
import { ref, onMounted } from 'vue';

const props = defineProps({
    voters: {
        type: Object,
        default: () => ({ data: [] })
    }
});

const form = useForm({
  settings: 0,
})

const showOnlyVerified = ref(false);
const currentStatus = ref(0);

// Fetch initial status
const fetchStatus = () => {
    form.get(route('voters.feature-status'), {
        preserveState: true,
        onSuccess: (response) => {
            currentStatus.value = response.status;
        },
        onError: () => {
            currentStatus.value = 0;
        }
    });
};

// Fetch status on component mount
onMounted(() => {
    fetchStatus();
});

const handleToggleSubmit = () => {
    form.post(route('voters.feature-status'), {
        preserveState: true,
        onSuccess: (response) => {
            currentStatus.value = response.status;
        },
        onError: () => {
            currentStatus.value = 0;
        }
    });
};
</script>

<template>
    <Head title="Voters List" />
    <AuthenticatedLayout>
        <div class="py-5">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                      <div class="flex justify-between items-center w-full">
                        <Link :href="route('voters.create')">Import data</Link>
                        <div class="flex items-center space-x-2">
                            <form @submit.prevent="handleToggleSubmit" class="flex items-center space-x-4">
                               
                                <button 
                                    type="submit" 
                                    class="px-4 py-2 text-white rounded transition-colors"
                                    :class="currentStatus === 1 ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-500 hover:bg-blue-600'"
                                >
                                    {{ currentStatus === 0 ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                      </div>
                        <h2 class="text-2xl font-semibold mb-4">Voters List</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">First Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Last Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Middle Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider text-center">UUID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Prec. No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
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
                                                :size="50"
                                                level="H"
                                                class="border border-gray-200 rounded"
                                            />
                                            {{ voter.uuid }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ voter.prec_no }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span 
                                                class="px-2 py-1 rounded-full text-xs font-semibold"
                                                :class="voter.status === 1 ? 'bg-red-400 text-red-900' : 'bg-green-400 text-green-900'"
                                            >
                                                {{ voter.status === 1 ? 'Scanned' : 'Not Scanned' }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 flex justify-center">
                            <nav class="inline-flex space-x-2">
                                <div>
            <p class="text-sm text-gray-700">
              Showing
              <span class="font-medium">{{ voters?.from ?? 0 }}</span>
              to
              <span class="font-medium">{{ voters?.to ?? 0 }}</span>
              of
              <span class="font-medium">{{ voters?.total ?? 0 }}</span>
              results
            </p>
          </div>
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
