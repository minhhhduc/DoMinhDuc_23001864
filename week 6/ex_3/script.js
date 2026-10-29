const { createApp } = Vue;

createApp({
    data() {
        return {
            tasks: [
                { id: 1, name: 'Học Vue.js cơ bản', completed: false },
                { id: 2, name: 'Làm bài tập tuần 6', completed: false },
                { id: 3, name: 'Đi chợ', completed: true }
            ],
            newTaskName: '',
            errorMessage: '',
            activeFilter: 'all',
            nextId: 4,
            filters: [
                { value: 'all', label: 'Tất cả' },
                { value: 'active', label: 'Chưa hoàn thành' },
                { value: 'completed', label: 'Đã hoàn thành' }
            ]
        };
    },
    computed: {
        totalCount() {
            return this.tasks.length;
        },
        completedCount() {
            return this.tasks.filter(task => task.completed).length;
        },
        activeCount() {
            return this.totalCount - this.completedCount;
        },
        filteredTasks() {
            if (this.activeFilter === 'active') {
                return this.tasks.filter(task => !task.completed);
            }
            if (this.activeFilter === 'completed') {
                return this.tasks.filter(task => task.completed);
            }
            return this.tasks;
        },
        emptyState() {
            if (this.totalCount === 0) {
                return {
                    title: 'Danh sách đang trống',
                    message: 'Thêm công việc đầu tiên của bạn ở ô phía trên.'
                };
            }
            if (this.activeFilter === 'active') {
                return {
                    title: 'Không có việc chưa hoàn thành',
                    message: 'Bạn đã hoàn thành hết công việc rồi!'
                };
            }
            if (this.activeFilter === 'completed') {
                return {
                    title: 'Chưa có việc nào hoàn thành',
                    message: 'Đánh dấu vào ô vuông để hoàn thành một công việc.'
                };
            }
            return {
                title: 'Không có công việc phù hợp',
                message: 'Hãy thử chọn bộ lọc khác.'
            };
        }
    },
    methods: {
        addTask() {
            const name = this.newTaskName.trim();
            if (name === '') {
                this.errorMessage = 'Tên công việc không được để trống.';
                return;
            }
            this.tasks.push({
                id: this.nextId++,
                name: name,
                completed: false
            });
            this.newTaskName = '';
            this.errorMessage = '';
        },
        removeTask(id) {
            this.tasks = this.tasks.filter(task => task.id !== id);
        },
        clearCompleted() {
            this.tasks = this.tasks.filter(task => !task.completed);
        }
    }
}).mount('#app');
