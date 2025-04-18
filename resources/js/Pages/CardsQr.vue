<template>
  <div class="py-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 px-2">
      <div v-for="voter in voters" :key="voter.id" 
        class="bg-gradient-to-b from-[#fdf9c4] to-[#ffffff] rounded-xl border border-gray-300 shadow-md font-sans relative overflow-hidden">
        <div class="p-4">
          <!-- Header -->
          <div class="flex justify-center items-center">
            <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center text-xs font-bold text-white">
              💡
            </div>
            <div class="text-xl font-extrabold text-[#118c22] uppercase">Team Madayaw</div>
          </div>

          <!-- Name and Details -->
          <div class="grid grid-cols-3 gap-4 mt-4">
            <div class="col-span-2 flex flex-col justify-center space-y-1">
              <div><span class="font-bold">Name:</span> {{ voter.last_name }}, {{ voter.first_name }} {{ voter.middle_name }}</div>
              <div><span class="font-bold">Barangay:</span> {{ voter.barangay || 'NA' }}</div>
              <div><span class="font-bold">Purok:</span> {{ voter.purok || 'NA' }}</div>
              <div><span class="font-bold">PCC:</span> <span class="text-gray-500">—</span></div>
            </div>
            <div class="flex flex-col items-center justify-center">
              <div class="">
                <QRCodeVue3
                                                :value="voter.uuid"
                                                :size="100"
                                                level="H"
                                                class="border border-gray-200 rounded"
                                            />
              </div>
              <div class="text-xs mt-1"><span class="font-bold">Precinct No:</span> {{ voter.prec_no }}</div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="bg-yellow-500 text-black text-center py-3 font-bold text-sm tracking-wide">
          PUROK CAMPAIGN COORDINATOR
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8 px-4">
      <div class="flex items-center justify-between bg-white p-4 rounded-lg shadow">
        <div class="flex-1 flex justify-between sm:hidden">
          <a v-if="voters.prev_page_url" 
             :href="voters.prev_page_url"
             class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Previous
          </a>
          <a v-if="voters.next_page_url" 
             :href="voters.next_page_url"
             class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Next
          </a>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Showing
              <span class="font-medium">{{ voters.from }}</span>
              to
              <span class="font-medium">{{ voters.to }}</span>
              of
              <span class="font-medium">{{ voters.total }}</span>
              results
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <!-- Previous Page Link -->
              <a v-if="voters.prev_page_url"
                 :href="voters.prev_page_url"
                 class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <span class="sr-only">Previous</span>
                ←
              </a>

              <!-- Pagination Elements -->
              <template v-for="(link, i) in voters.links" :key="i">
                <div v-if="link.url === null" 
                     class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                  ...
                </div>
                <a v-else
                   :href="link.url"
                   :class="[
                       link.active ? 'z-10 bg-yellow-50 border-yellow-500 text-yellow-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                       'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                   ]"
                   v-html="link.label">
                </a>
              </template>

              <!-- Next Page Link -->
              <a v-if="voters.next_page_url"
                 :href="voters.next_page_url"
                 class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <span class="sr-only">Next</span>
                →
              </a>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import QRCodeVue3 from 'qrcode.vue';

const props = defineProps({
  voters: {
    type: Object,
    default: () => ({
      data: [],
      current_page: 1,
      per_page: 15,
      last_page: 1,
      total: 0,
      from: 0,
      to: 0,
      links: [],
      prev_page_url: null,
      next_page_url: null
    })
  }
});

const generateQRValue = (voter) => {
  return JSON.stringify({
    id: voter.id,
    name: `${voter.last_name}, ${voter.first_name} ${voter.middle_name}`,
    precinct: voter.prec_no,
    uuid: voter.uuid
  });
};
</script>

<style scoped>
.container {
  -webkit-print-color-adjust: exact;
  print-color-adjust: exact;
}
</style>