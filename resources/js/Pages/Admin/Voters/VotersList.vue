<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

import QRCodeVue3 from 'qrcode.vue';
import { ref } from 'vue';

const props = defineProps({
    voters: {
        type: Array,
        default: () => []
    }
});

// Modal state
const isModalOpen = ref(false);

// Form setup
const form = useForm({
    first_name: '',
    last_name: '',
    middle_name: ''
});

const openModal = () => {
    form.reset();
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const submitForm = () => {
    form.post(route('voters.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            closeModal();
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
                        <div class="w-full flex justify-end items-center mb-4">
                            <button @click="openModal" class="bg-yellow-400 hover:bg-yellow-500 text-white px-6 py-2 rounded-lg transition duration-150">
                                Add Voter
                            </button>
                        </div>

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
                                    <tr v-if="props.voters.length === 0">
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No voters found
                                        </td>
                                    </tr>
                                    <tr v-for="voter in props.voters" :key="voter.id">
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
                                            {{ voter.uuid }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative">
                <h2 class="text-xl font-bold mb-4">Add New Voter</h2>

                <form @submit.prevent="submitForm" class="space-y-6">
                    <div class="grid grid-cols-1  gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">First Name</label>
                            <input v-model="form.first_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500" :class="{ 'border-red-500': form.errors.first_name }"/>
                            <p v-if="form.errors.first_name" class="mt-1 text-sm text-red-600">{{ form.errors.first_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input v-model="form.last_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500" :class="{ 'border-red-500': form.errors.last_name }"/>
                            <p v-if="form.errors.last_name" class="mt-1 text-sm text-red-600">{{ form.errors.last_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Middle Name</label>
                            <input v-model="form.middle_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500" :class="{ 'border-red-500': form.errors.middle_name }"/>
                            <p v-if="form.errors.middle_name" class="mt-1 text-sm text-red-600">{{ form.errors.middle_name }}</p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            {{ form.processing ? 'Creating...' : 'Create Voter' }}
                        </button>
                    </div>
                </form>

                <!-- Close button -->
                <button @click="closeModal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl">&times;</button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
