<template>
  <div class="p-4">
    <h2 class="text-xl mb-4">Item CRUD</h2>
    <input v-model="newItem" placeholder="Enter name" class="border p-2 mr-2" />
    <button @click="createItem" class="bg-blue-500 text-white px-3 py-2">Add</button>

    <ul class="mt-4">
      <li v-for="item in items" :key="item.id" class="flex justify-between mb-2">
        <input v-model="item.name" class="border p-1 mr-2" />
        <button @click="updateItem(item)">Update</button>
        <button @click="deleteItem(item.id)" class="text-red-500 ml-2">Delete</button>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const items = ref([])
const newItem = ref('')

const fetchItems = async () => {
  const res = await axios.get('/api/items')
  items.value = res.data
}

const createItem = async () => {
  if (!newItem.value) return
  await axios.post('/api/items', { name: newItem.value })
  newItem.value = ''
  fetchItems()
}

const updateItem = async (item) => {
  await axios.put(`/api/items/${item.id}`, { name: item.name })
  fetchItems()
}

const deleteItem = async (id) => {
  await axios.delete(`/api/items/${id}`)
  fetchItems()
}

onMounted(fetchItems)
</script>
