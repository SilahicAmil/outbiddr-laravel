<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type WorkOrder, type Bid } from '@/types';
import { route } from 'ziggy-js';
import { Edit, MapPin, Calendar, User, Building2, FileText, DollarSign, Check, X, Clock, MessageSquare, Send, Pencil, Trash2 } from 'lucide-vue-next';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { edit } from '@/routes/workorders';

interface Note {
    id: number;
    user_id: number;
    user_name: string;
    content: string;
    created_at: string;
    updated_at: string;
}

const props = defineProps<{
    workOrder: { data: WorkOrder };
    bids: Bid[];
    notes: Note[];
    isOwner: boolean;
    currentUserId: number;
}>();

const wo_id = props.workOrder?.data.id ?? 'NULL';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Work Orders', href: route('workorders') },
    {
        title: 'View Work Order',
        href: route('workorders.show', { workOrder: wo_id }),
    },
];

// Bid action states
const processingBids = reactive<Record<number, boolean>>({});
const bidStatuses = reactive<Record<number, string>>({});

// Initialize bid statuses from props
if (props.bids) {
    props.bids.forEach((bid: Bid) => {
        bidStatuses[bid.id] = bid.status;
    });
}

// Notes state
const notesList = ref<Note[]>(props.notes || []);
const newNoteContent = ref('');
const isAddingNote = ref(false);
const editingNoteId = ref<number | null>(null);
const editingNoteContent = ref('');
const processingNotes = reactive<Record<number, boolean>>({});

const getStatusColor = (status: string) => {
    const statusLower = status.toLowerCase();
    if (statusLower === 'assigned') return 'bg-green-400 text-black';
    if (statusLower === 'open') return 'bg-yellow-400 text-black';
    if (statusLower === 'completed') return 'bg-red-400 text-black';
    return 'bg-gray-100 text-gray-800';
};

const getBidStatusColor = (status: string) => {
    if (status === 'accepted') return 'bg-green-500 text-white';
    if (status === 'rejected') return 'bg-red-500 text-white';
    return 'bg-gray-500 text-white';
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleString();
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
};

async function acceptBid(bidId: number) {
    processingBids[bidId] = true;

    try {
        const response = await fetch(route('bids.accept', { bid: bidId }), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                Accept: 'application/json',
            },
        });

        if (response.ok) {
            window.location.reload();
        }
    } catch (error) {
        console.error('Error accepting bid:', error);
    } finally {
        processingBids[bidId] = false;
    }
}

async function rejectBid(bidId: number) {
    processingBids[bidId] = true;

    try {
        const response = await fetch(route('bids.reject', { bid: bidId }), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                Accept: 'application/json',
            },
        });

        if (response.ok) {
            bidStatuses[bidId] = 'rejected';
        }
    } catch (error) {
        console.error('Error rejecting bid:', error);
    } finally {
        processingBids[bidId] = false;
    }
}

// Notes functions
async function addNote() {
    if (!newNoteContent.value.trim()) return;

    isAddingNote.value = true;

    try {
        const response = await fetch(route('notes.store', { workOrder: wo_id }), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                Accept: 'application/json',
            },
            body: JSON.stringify({ content: newNoteContent.value }),
        });

        if (response.ok) {
            const data = await response.json();
            notesList.value.unshift(data.note);
            newNoteContent.value = '';
        }
    } catch (error) {
        console.error('Error adding note:', error);
    } finally {
        isAddingNote.value = false;
    }
}

function startEditNote(note: Note) {
    editingNoteId.value = note.id;
    editingNoteContent.value = note.content;
}

function cancelEditNote() {
    editingNoteId.value = null;
    editingNoteContent.value = '';
}

async function saveEditNote(noteId: number) {
    if (!editingNoteContent.value.trim()) return;

    processingNotes[noteId] = true;

    try {
        const response = await fetch(route('notes.update', { note: noteId }), {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                Accept: 'application/json',
            },
            body: JSON.stringify({ content: editingNoteContent.value }),
        });

        if (response.ok) {
            const data = await response.json();
            const index = notesList.value.findIndex(n => n.id === noteId);
            if (index !== -1) {
                notesList.value[index] = data.note;
            }
            cancelEditNote();
        }
    } catch (error) {
        console.error('Error updating note:', error);
    } finally {
        processingNotes[noteId] = false;
    }
}

async function deleteNote(noteId: number) {
    if (!confirm('Are you sure you want to delete this note?')) return;

    processingNotes[noteId] = true;

    try {
        const response = await fetch(route('notes.destroy', { note: noteId }), {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                Accept: 'application/json',
            },
        });

        if (response.ok) {
            notesList.value = notesList.value.filter(n => n.id !== noteId);
        }
    } catch (error) {
        console.error('Error deleting note:', error);
    } finally {
        processingNotes[noteId] = false;
    }
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-8 p-6">
            <!-- Header with Edit Button -->
            <div class="flex items-center justify-between border-b pb-4">
                <div>
                    <h1 v-if="workOrder.data.title" class="text-3xl font-bold">
                        {{ workOrder.data.title }}
                    </h1>
                    <p class="text-lg text-gray-400 mt-1">
                        Work Order #{{ wo_id }}
                    </p>
                </div>
                <!-- Status Section -->
                <div class="flex items-center justify-end">
                    <Badge :class="getStatusColor(workOrder.data.status)">
                        <span class="font-bold">{{ workOrder.data.status.toUpperCase() }}</span>
                    </Badge>
                </div>
                <Link :href="edit({ workOrder: wo_id }).url" v-if="workOrder.data.status !== 'completed' && isOwner">
                    <Button variant="outline" class="cursor-pointer">
                        <Edit class="h-4 w-4 mr-2" />
                        Edit Work Order
                    </Button>
                </Link>
            </div>

            <!-- Main Content: Info and Map -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- General Information Section -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Description Section -->
                    <div class="border rounded-md p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <FileText class="h-5 w-5 text-gray-500" />
                            <h2 class="text-lg font-semibold">Description</h2>
                        </div>
                        <p class="text-gray-300 whitespace-pre-wrap">
                            {{ workOrder.data.description || 'No description provided' }}
                        </p>
                    </div>

                    <!-- Address Section -->
                    <div class="border rounded-md p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <MapPin class="h-5 w-5 text-gray-500" />
                            <h2 class="text-lg font-semibold">Address</h2>
                        </div>
                        <p class="text-gray-300">
                            {{ workOrder.data.address || 'No address provided' }}
                        </p>
                    </div>

                    <!-- Additional Information Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Owner Section -->
                        <div class="border rounded-md p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <Building2 class="h-5 w-5 text-gray-500" />
                                <h2 class="text-lg font-semibold">Owner</h2>
                            </div>
                            <p class="text-gray-300">
                                {{ workOrder.data.owner_name?.toUpperCase() }}
                            </p>
                        </div>

                        <!-- Assigned User Section -->
                        <div class="border rounded-md p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <User class="h-5 w-5 text-gray-500" />
                                <h2 class="text-lg font-semibold">Assigned To</h2>
                            </div>
                            <p class="text-gray-300">
                                {{ workOrder.data.assigned_user?.toUpperCase() || 'Unassigned' }}
                            </p>
                        </div>

                        <!-- Created Date Section -->
                        <div class="border rounded-md p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <Calendar class="h-5 w-5 text-gray-500" />
                                <h2 class="text-lg font-semibold">Created</h2>
                            </div>
                            <p class="text-gray-300">
                                {{ formatDate(workOrder.data.created_at) }}
                            </p>
                        </div>

                        <!-- Updated Date Section -->
                        <div class="border rounded-md p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <Calendar class="h-5 w-5 text-gray-500" />
                                <h2 class="text-lg font-semibold">Last Updated</h2>
                            </div>
                            <p class="text-gray-300">
                                {{ formatDate(workOrder.data.updated_at) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Map Section -->
                <div class="lg:col-span-1">
                    <div class="border rounded-md p-6">
                        <h2 class="text-lg font-semibold mb-4">Location</h2>
                        <div
                            class="w-full h-96 bg-gray-100 rounded-md flex items-center justify-center border-2 border-dashed border-gray-300"
                        >
                            <div class="text-center p-6">
                                <MapPin class="h-12 w-12 mx-auto text-gray-400 mb-4" />
                                <p class="text-gray-500 font-medium">Map Placeholder</p>
                                <p class="text-sm text-gray-400 mt-2">
                                    {{ workOrder.data.address || 'No address available' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bids Section (Only visible to owner when status is open) -->
            <div v-if="isOwner && bids && bids.length > 0" class="border rounded-md p-6">
                <div class="flex items-center gap-2 mb-6">
                    <DollarSign class="h-5 w-5 text-gray-500" />
                    <h2 class="text-lg font-semibold">Bids Received</h2>
                    <Badge class="ml-2 bg-primary/20 text-primary">
                        {{ bids.length }} {{ bids.length === 1 ? 'bid' : 'bids' }}
                    </Badge>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="bid in bids"
                        :key="bid.id"
                        class="border rounded-md p-4 flex items-center justify-between"
                        :class="{
                            'border-green-500 bg-green-500/10': bidStatuses[bid.id] === 'accepted',
                            'border-red-500/50 bg-red-500/5 opacity-60': bidStatuses[bid.id] === 'rejected',
                        }"
                    >
                        <div class="flex items-center gap-4">
                            <div class="bg-muted rounded-full p-3">
                                <User class="h-5 w-5 text-gray-400" />
                            </div>
                            <div>
                                <p class="font-semibold">{{ bid.bidder_name }}</p>
                                <p class="text-sm text-gray-400">
                                    <Clock class="h-3 w-3 inline mr-1" />
                                    {{ formatDate(bid.created_at) }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-2xl font-bold text-primary">
                                    {{ formatCurrency(bid.amount) }}
                                </p>
                            </div>

                            <!-- Bid Status Badge -->
                            <Badge
                                v-if="bidStatuses[bid.id] !== 'pending'"
                                :class="getBidStatusColor(bidStatuses[bid.id])"
                            >
                                {{ bidStatuses[bid.id].toUpperCase() }}
                            </Badge>

                            <!-- Action Buttons (only for pending bids when WO is open) -->
                            <div
                                v-if="bidStatuses[bid.id] === 'pending' && workOrder.data.status.toLowerCase() === 'open'"
                                class="flex items-center gap-2"
                            >
                                <Button
                                    size="sm"
                                    @click="acceptBid(bid.id)"
                                    :disabled="processingBids[bid.id]"
                                    class="bg-green-600 hover:bg-green-700"
                                >
                                    <Check class="h-4 w-4 mr-1" />
                                    Accept
                                </Button>
                                <Button
                                    size="sm"
                                    variant="outline"
                                    @click="rejectBid(bid.id)"
                                    :disabled="processingBids[bid.id]"
                                    class="border-red-500 text-red-500 hover:bg-red-500/10"
                                >
                                    <X class="h-4 w-4 mr-1" />
                                    Reject
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No pending bids message -->
                <div
                    v-if="workOrder.data.status !== 'open'"
                    class="mt-4 p-4 border border-dashed rounded-md text-center text-gray-400"
                >
                    This work order is no longer accepting bids.
                </div>
            </div>

            <!-- Empty bids state for owner -->
            <div
                v-else-if="isOwner && workOrder.data.status === 'open'"
                class="border rounded-md p-6"
            >
                <div class="flex items-center gap-2 mb-4">
                    <DollarSign class="h-5 w-5 text-gray-500" />
                    <h2 class="text-lg font-semibold">Bids</h2>
                </div>
                <div class="text-center py-8">
                    <DollarSign class="h-12 w-12 mx-auto text-gray-400 mb-4" />
                    <p class="text-gray-400">No bids received yet.</p>
                    <p class="text-sm text-gray-500 mt-1">Bids will appear here when contractors submit them.</p>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="border rounded-md p-6">
                <div class="flex items-center gap-2 mb-6">
                    <MessageSquare class="h-5 w-5 text-gray-500" />
                    <h2 class="text-lg font-semibold">Work Order Notes</h2>
                    <Badge v-if="notesList.length > 0" class="ml-2 bg-primary/20 text-primary">
                        {{ notesList.length }} {{ notesList.length === 1 ? 'note' : 'notes' }}
                    </Badge>
                </div>

                <!-- Add Note Form -->
                <div class="mb-6">
                    <textarea
                        v-model="newNoteContent"
                        rows="3"
                        placeholder="Add a note..."
                        class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:border-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                        :disabled="isAddingNote"
                    ></textarea>
                    <div class="flex justify-end mt-2">
                        <Button @click="addNote" :disabled="isAddingNote || !newNoteContent.trim()">
                            <Send class="h-4 w-4 mr-2" />
                            {{ isAddingNote ? 'Adding...' : 'Add Note' }}
                        </Button>
                    </div>
                </div>

                <!-- Notes List -->
                <div v-if="notesList.length > 0" class="space-y-4">
                    <div
                        v-for="note in notesList"
                        :key="note.id"
                        class="border rounded-md p-4"
                    >
                        <!-- Note Header -->
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="bg-muted rounded-full p-2">
                                    <User class="h-4 w-4 text-gray-400" />
                                </div>
                                <div>
                                    <p class="font-semibold text-sm">{{ note.user_name }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ formatDate(note.created_at) }}
                                        <span v-if="note.updated_at !== note.created_at" class="italic">
                                            (edited)
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Edit/Delete buttons (only for note author) -->
                            <div v-if="note.user_id === currentUserId && editingNoteId !== note.id" class="flex items-center gap-1">
                                <Button
                                    size="sm"
                                    variant="ghost"
                                    @click="startEditNote(note)"
                                    :disabled="processingNotes[note.id]"
                                    class="h-8 w-8 p-0"
                                >
                                    <Pencil class="h-4 w-4" />
                                </Button>
                                <Button
                                    size="sm"
                                    variant="ghost"
                                    @click="deleteNote(note.id)"
                                    :disabled="processingNotes[note.id]"
                                    class="h-8 w-8 p-0 text-red-500 hover:text-red-600 hover:bg-red-500/10"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>

                        <!-- Note Content (View Mode) -->
                        <div v-if="editingNoteId !== note.id" class="pl-10">
                            <p class="text-gray-300 whitespace-pre-wrap">{{ note.content }}</p>
                        </div>

                        <!-- Note Content (Edit Mode) -->
                        <div v-else class="pl-10">
                            <textarea
                                v-model="editingNoteContent"
                                rows="3"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:border-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                :disabled="processingNotes[note.id]"
                            ></textarea>
                            <div class="flex justify-end gap-2 mt-2">
                                <Button
                                    size="sm"
                                    variant="outline"
                                    @click="cancelEditNote"
                                    :disabled="processingNotes[note.id]"
                                >
                                    Cancel
                                </Button>
                                <Button
                                    size="sm"
                                    @click="saveEditNote(note.id)"
                                    :disabled="processingNotes[note.id] || !editingNoteContent.trim()"
                                >
                                    {{ processingNotes[note.id] ? 'Saving...' : 'Save' }}
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty Notes State -->
                <div v-else class="text-center py-8">
                    <MessageSquare class="h-12 w-12 mx-auto text-gray-400 mb-4" />
                    <p class="text-gray-400">No notes yet.</p>
                    <p class="text-sm text-gray-500 mt-1">Add a note to communicate with others on this work order.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
