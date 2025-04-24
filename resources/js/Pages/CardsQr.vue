<template>
  <Head title="Cards"/>
   
  <div class="py-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 px-2">
      <div v-for="voter in voters" :key="voter?.id" 
        class="bg-gradient-to-b from-[#fdf9c4] to-[#ffffff] rounded-xl border border-gray-300 shadow-md font-sans overflow-hidden relative">
        <img src="assets/bgg.png" alt="" class="absolute inset-0 w-full h-full object-cover opacity-15">

     <div class="relative">
        <div class="p-4">
          <!-- Header -->
          <div class="flex justify-center items-center">
            <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center text-xs font-bold text-white overflow-hidden">
              <img src="assets/thumb.png" alt="" class="object-cover">
            </div>
            <div class="text-3xl font-extrabold text-black ml-2 uppercase">KANATO INI !</div>
          </div>
          <!-- Name and Details -->
          <div class="grid grid-cols-3 gap-4 mt-4">
            <div class="col-span-2 flex flex-col justify-center space-y-1">
              <div><span class="font-bold">Name:</span> {{ voter?.last_name ?? '' }}, {{ voter?.first_name ?? '' }} {{ voter?.middle_name ?? '' }}</div>
              <div><span class="font-bold">Barangay:</span> {{ voter?.category?.barangay ?? 'NA' }}</div>
              <div><span class="font-bold">BCM:</span> {{ voter?.category?.purok ?? 'NA' }}</div>
              <!-- <div><span class="font-bold">PCC:</span> <span class="text-gray-500">—</span></div> -->
            </div>
            <div class="flex flex-col items-center justify-center">
              <div class="">
                <QRCodeVue3
                  :value="voter?.uuid ?? ''"
                  :size="120"
                  level="H"
                  class="border border-gray-200 rounded"
                />
              </div>
              <div class="text-xs mt-1"><span class="font-bold">Precinct No:</span> {{ voter?.prec_no ?? 'NA' }}</div>
            </div>
          </div>
        </div>
        <!-- Footer -->
        <div class="bg-yellow-500 text-black text-center py-1 font-bold text-sm tracking-wide flex flex-col">
          <span>JENIFFER BIONG</span>
          <span class="text-xs">MCM Parallel</span>
        </div>
     </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import QRCodeVue3 from 'qrcode.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
  voters: {
    type: Array,
    default: () => []
  }
});

const generateQRValue = (voter) => {
  if (!voter) return '';
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