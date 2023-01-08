<template>
    <div class="w-6/12 p-10 mx-auto">
        <div class="flex justify-between">
            <h1 class="text-2xl">Todo</h1>
            <span class="capitalize"
                >Welcome {{ user && user.name }},
                <button
                    class="text-orange-500 underline hover:no-underline rounded-md"
                    @click="handleLogout"
                >
                    Logout
                </button></span
            >
        </div>
        <form v-on:submit.prevent="addTodo" method="post">
            <input
                type="text"
                class="p-2 w-64 border rounded-md"
                v-model="todo"
                placeholder="Enter your todo"
            />
            <button
                type="submit"
                class="bg-blue-600 text-white px-5 py-2 rounded-md ml-2 hover:bg-blue-400"
            >
                Add
            </button>
        </form>
        <Loader v-if="isLoading" />
        <div class="form-check mt-5 cursor-pointer">
            <input
                class="form-check-input appearance-none h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer"
                type="checkbox"
                :checked="showAllTodo"
                id="flexCheckChecked"
                @click="showAll()"
            />
            <label
                class="form-check-label inline-block text-gray-800"
                for="flexCheckChecked"
            >
                Show All
            </label>
        </div>
        <ul class="border-t mt-3 cursor-pointer">
            <li
                v-for="(val, idx) in todos"
                :class="`py-3 border-b text-gray-600 ${
                    val.status ? 'opacity-40' : ''
                }`"
                :key="idx"
            >
                <input
                    type="checkbox"
                    :checked="val.status"
                    @click="checked(val, idx)"
                />
                <span @click="checked(val, idx)" class="pl-3"
                    >{{ val.title }}
                </span>
                <button
                    v-if="!val.status"
                    class="float-right bg-red-400 px-2 text-white font-bold rounded-md hover:bg-red-600"
                    @click="deleteTodo(val, idx)"
                >
                    &times;
                </button>
            </li>
        </ul>
    </div>
</template>
<script>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { request } from "../helper";
import Loader from "../components/Loader.vue";

export default {
    components: {
        Loader,
    },
    setup() {
        const todo = ref("");
        const todos = ref([]);
        const user = ref();
        const isLoading = ref();
        const showAllTodo = ref(0);
        let router = useRouter();
        onMounted(() => {
            authentication();
            handleTodos();
        });

        const authentication = async () => {
            isLoading.value = true;
            try {
                const req = await request("get", "/api/user");
                user.value = req.data;
            } catch (e) {
                await router.push("/");
            }
        };

        const handleTodos = async () => {
            try {
                const req = await request(
                    "get",
                    `/api/todos?all=${showAllTodo.value}`
                );
                todos.value = req.data.data;
            } catch (e) {
                console.log(e.message);
            }
            isLoading.value = false;
        };

        const handleNewTodo = async (title) => {
            try {
                const data = { title: title };
                const req = await request("post", "/api/todos", data);
                if (req.data.message) {
                    isLoading.value = false;
                    todos.value.push(req.data.data);
                    return alert(req.data.message);
                }
            } catch (e) {
                return alert(e.response.data.message);
            }
            isLoading.value = false;
        };

        const handleLogout = () => {
            localStorage.removeItem("APP_DEMO_USER_TOKEN");
            router.push("/");
        };

        const addTodo = () => {
            if (todo.value === "") {
                return alert("Todo cannot be empty");
            }
            isLoading.value = true;
            handleNewTodo(todo.value);
            todo.value = "";
        };

        const checked = async (val, index) => {
            try {
                const data = { status: !val.status };
                const req = await request("put", `/api/todos/${val.id}`, data);
                if (req.data.message) {
                    isLoading.value = false;
                    todos.value[index].status = !val.status;
                    if (!showAllTodo.value) {
                        todos.value.splice(index, 1);
                    }
                    return alert(req.data.message);
                }
            } catch (e) {
                await router.push("/");
            }
            isLoading.value = false;
        };

        const deleteTodo = async (val, index) => {
            if (window.confirm("Are u sure to delete this task?")) {
                try {
                    const req = await request("delete", `/api/todos/${val.id}`);
                    if (req.data.message) {
                        isLoading.value = false;
                        todos.value.splice(index, 1);
                    }
                } catch (e) {
                    await router.push("/");
                }
                isLoading.value = false;
            }
        };

        const showAll = async () => {
            showAllTodo.value = showAllTodo.value ? 0 : 1;
            console.log(showAllTodo.value);
            handleTodos();
        };

        return {
            todo,
            todos,
            user,
            checked,
            addTodo,
            isLoading,
            deleteTodo,
            handleLogout,
            showAllTodo,
            showAll,
        };
    },
};
</script>
