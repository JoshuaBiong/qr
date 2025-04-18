<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  categories: Array, // Pass from backend
})

const form = useForm({
  category_id: '',
  file: null,
})

const handleFileChange = (e) => {
  form.file = e.target.files[0]
}

const submit = () => {
  form.post(route('voters.import'), {
    forceFormData: true,
    onSuccess: () => form.reset(),
  })
}
</script>

<template>
  <div class="max-w-3xl mx-auto p-6 bg-white rounded shadow">
    <h2 class="text-xl font-bold mb-4">Import Voters CSV/XLSX</h2>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium">Select Category</label>
        <select v-model="form.category_id" class="w-full border rounded p-2">
          <option disabled value="">-- Choose Category --</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.barangay }} - {{ category.purok }}
          </option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium">Upload File</label>
        <input type="file" @change="handleFileChange" accept=".csv, .xlsx" />
      </div>

      <button
        type="submit"
        :disabled="form.processing"
        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
      >
        {{ form.processing ? 'Uploading...' : 'Import Voters' }}
      </button>
    </form>
  </div>
</template>



<script setup>

</script>