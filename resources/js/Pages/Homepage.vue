<script setup>
import { ref, onMounted, nextTick, watch, onUnmounted } from 'vue';
import { QrcodeStream } from 'vue-qrcode-reader';
// import { router } from '@inertiajs/vue3';

// Add props for Inertia data
const props = defineProps({
    verification: {
        type: Object,
        default: () => null
    }
});

const scanning = ref(false);
const result = ref('');
const error = ref('');
const verificationStatus = ref(null);
const voterData = ref(null);
const camera = ref('auto');
const facingMode = ref('environment');
const isVerifying = ref(false);
const loading = ref(false);
const hasCamera = ref(false);
const cameraError = ref('');
const isInDatabase = ref(false);
const isComponentMounted = ref(false);
const isScannerReady = ref(false);
const showVerificationModal = ref(false);
const paused = ref(false);
const showScanConfirmation = ref(false);
const canScanAgain = ref(true);
const cooldownInterval = ref(null);

// Watch for verification prop changes
watch(() => props.verification, (newVerification) => {
    if (newVerification) {
        if (newVerification.success) {
            verificationStatus.value = 'success';
            voterData.value = newVerification.data;
            isInDatabase.value = true;
            showVerificationModal.value = true;
            stopScanning();
        } else {
            verificationStatus.value = 'error';
            error.value = newVerification.message;
            isInDatabase.value = false;
            showVerificationModal.value = true;
        }
    }
}, { immediate: true });

const checkCamera = async () => {
    try {
        if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
            hasCamera.value = false;
            cameraError.value = 'Camera access is not supported in this browser or insecure context.';
            return;
        }

        const devices = await navigator.mediaDevices.enumerateDevices();
        hasCamera.value = devices.some(device => device.kind === 'videoinput');

        if (!hasCamera.value) {
            cameraError.value = 'No camera found on this device.';
        }
    } catch (err) {
        hasCamera.value = false;
        cameraError.value = 'Failed to access camera. Please check permissions.';
        // console.error('Camera error:', err);
    }
};

const onCameraOn = () => {
    showScanConfirmation.value = false;
    // console.log('Camera turned on');
};

const onCameraOff = () => {
    showScanConfirmation.value = true;
    // console.log('Camera turned off');
};

const onError = (err) => {
    // console.error('QR Code Error:', err);

    error.value = err.message;
};

const timeout = (ms) => {
    return new Promise((resolve) => {
        window.setTimeout(resolve, ms);
    });
};

const onDetect = async (detectedCodes) => {
    if (isVerifying.value || verificationStatus.value === 'success' || !canScanAgain.value) return;

    const uuid = detectedCodes[0]?.rawValue;
    if (!uuid) return;

    result.value = uuid;
    isVerifying.value = true;
    paused.value = true;

    try {
        await verifyUUID(uuid);
    } finally {
        await timeout(200); // Reduced from 500ms to 200ms
        paused.value = false;
        isVerifying.value = false;
    }
};

const verifyUUID = async (uuid) => {
    // console.log('Starting verification for UUID:', uuid);
    loading.value = true;
    verificationStatus.value = null;
    error.value = '';
    voterData.value = null;
    isInDatabase.value = false;

    try {
        // console.log('Sending verification request...');
        const response = await fetch(route('voters.verify', { uuid }));
        const data = await response.json();
        // console.log('Server response:', data);

        if (data.success) {
            // console.log('✅ Verification successful - Voter found in database');
            verificationStatus.value = 'success';
            voterData.value = data.data;
            isInDatabase.value = true;
            showVerificationModal.value = true;
            stopScanning();
        } else {
            // console.log('❌ Verification failed');
            verificationStatus.value = 'error';
            error.value = data.message || 'Verification failed';
            isInDatabase.value = false;
            showVerificationModal.value = true;
        }
    } catch (err) {
        console.error('❌ Verification error:', err);
        verificationStatus.value = 'error';
        error.value = 'Failed to verify voter. Please try again.';
        showVerificationModal.value = true;
    } finally {
        // console.log('Verification process completed');
        loading.value = false;
    }
};

const closeModal = () => {
    showVerificationModal.value = false;
    canScanAgain.value = false;
    
    // Start cooldown timer with shorter duration
    cooldownInterval.value = setInterval(() => {
        canScanAgain.value = true;
        clearInterval(cooldownInterval.value);
    }, 1000); // Reduced from 3000ms to 1000ms
    
    startScanning();
};

const startScanning = () => {
    if (!hasCamera.value) {
        error.value = cameraError.value;
        return;
    }

    scanning.value = true;
    result.value = '';
    error.value = '';
    verificationStatus.value = null;
    voterData.value = null;
};

const stopScanning = () => {
    scanning.value = false;
};

onMounted(async () => {
    try {
        await nextTick();
        isComponentMounted.value = true;

        if (!navigator.mediaDevices) {
            cameraError.value = 'Your browser does not support camera access.';
            return;
        }

        await checkCamera();
    } catch (err) {
        console.error('Component mounting error:', err);
        cameraError.value = 'Failed to initialize scanner. Please refresh the page.';
    }
});

// Cleanup interval on component unmount
onUnmounted(() => {
    if (cooldownInterval.value) {
        clearInterval(cooldownInterval.value);
    }
});
</script>

<template>
    <div class="h-screen w-full bg-gray-800 flex justify-center items-center flex-col px-4">
        <h1 class="text-4xl font-bold mb-6 text-white">Voter QR Scanner</h1>
        <div v-if="!hasCamera" class="text-center p-4 bg-red-100 rounded-lg mb-4">
            <p class="text-red-600">{{ cameraError }}</p>
        </div>

        <div v-if="!scanning" class="text-center">
            <button 
                @click="startScanning" 
                class="bg-yellow-400 px-10 py-3 font-bold rounded-lg hover:bg-yellow-500 hover:px-14 duration-200 transition-all  disabled:opacity-50"
                :disabled="!hasCamera || !isComponentMounted"
            >
                Start Scanning
            </button>
        </div>

        <div v-else class="w-full max-w-md">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="relative">
                    <QrcodeStream 
                        :paused="paused"
                        @detect="onDetect"
                        @camera-on="onCameraOn"
                        @camera-off="onCameraOff"
                        @error="onError"
                        class="w-full"
                    >
                        
                    </QrcodeStream>
                </div>

                <div v-if="loading" class="mt-4 text-center text-yellow-500 font-semibold">
                    <span class="animate-pulse">Verifying...</span>
                </div>

                <div v-if="error && verificationStatus !== 'success'" class="mt-4 text-red-500 text-center">
                    {{ error }}
                </div>

                <button 
                    @click="stopScanning" 
                    class="mt-6 bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 w-full"
                >
                    Stop Scanning
                </button>
            </div>
        </div>

        <!-- Verification Modal -->
        <div v-if="showVerificationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <div v-if="verificationStatus === 'success'" class="text-center">
                    <div class="text-green-500 text-6xl mb-4">✅</div>
                    <h3 class="text-xl font-bold text-green-800 mb-2">Voter Found in Database</h3>
                    <div v-if="voterData" class="text-left mt-4 space-y-2">
                        <p class="text-gray-700">
                            <span class="font-semibold">Name:</span> 
                            {{ voterData.first_name }} {{ voterData.middle_name }} {{ voterData.last_name }}
                        </p>
                        <p class="text-gray-700">
                            <span class="font-semibold">UUID:</span> 
                            {{ voterData.uuid }}
                        </p>
                        <p class="text-gray-700">
                            <span class="font-semibold">Status:</span> 
                            <span class="text-green-600">Verified</span>
                        </p>
                    </div>
                </div>
                
                <div v-else class="text-center">
                    <div class="text-red-500 text-6xl mb-4">❌</div>
                    <h3 class="text-xl font-bold text-red-800 mb-2">Verification Failed</h3>
                    <p class="text-gray-700">{{ error }}</p>
                </div>

                <button 
                    @click="closeModal"
                    class="mt-6 w-full bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
                >
                    Scan Again
                </button>
            </div>
        </div>
    </div>
</template>


