const { createApp } = Vue;

const API_URL = 'api/products.php';
const PAGE_SIZE = 5;

createApp({
    data() {
        return {
            products: [],
            pagination: { page: 1, limit: PAGE_SIZE, total: 0, total_pages: 1 },
            loading: false,
            saving: false,
            message: '',
            messageType: 'success',
            editingId: null,
            showForm: false,
            formError: '',
            form: { name: '', price: null, stock: null },
            requestSequence: 0
        };
    },
    computed: {
        canPrev() {
            return this.pagination.page > 1;
        },
        canNext() {
            return this.pagination.page < this.pagination.total_pages;
        }
    },
    watch: {
        showForm(value) {
            document.body.style.overflow = value ? 'hidden' : '';
        }
    },
    mounted() {
        this.loadProducts(1);
    },
    beforeUnmount() {
        document.body.style.overflow = '';
    },
    methods: {
        showMessage(text, type = 'success') {
            this.message = text;
            this.messageType = type;
        },
        async loadProducts(page) {
            if (page === undefined) {
                page = this.pagination.page;
            }

            const sequence = ++this.requestSequence;
            this.loading = true;

            try {
                const response = await fetch(`${API_URL}?page=${page}&limit=${PAGE_SIZE}`);
                const result = await response.json();

                if (sequence !== this.requestSequence) return;

                if (!response.ok || !result.success) {
                    this.showMessage(result.message || 'Không tải được danh sách sản phẩm', 'error');
                    return;
                }

                this.products = result.data;
                Object.assign(this.pagination, result.pagination);
            } catch {
                if (sequence === this.requestSequence) {
                    this.showMessage('Không thể kết nối API', 'error');
                }
            } finally {
                if (sequence === this.requestSequence) {
                    this.loading = false;
                }
            }
        },
        resetForm() {
            this.editingId = null;
            this.showForm = false;
            this.formError = '';
            this.form.name = '';
            this.form.price = null;
            this.form.stock = null;
        },
        openAddForm() {
            this.resetForm();
            this.showForm = true;
            this.$nextTick(() => {
                if (this.$refs.nameInput) {
                    this.$refs.nameInput.focus();
                }
            });
        },
        editProduct(product) {
            this.resetForm();
            this.editingId = product.id;
            this.form.name = product.name;
            this.form.price = product.price;
            this.form.stock = product.stock;
            this.showForm = true;
            this.$nextTick(() => {
                if (this.$refs.nameInput) {
                    this.$refs.nameInput.focus();
                }
            });
        },
        validateForm() {
            const name = (this.form.name || '').trim();
            if (name.length < 1 || name.length > 100) {
                return 'Tên sản phẩm phải từ 1 đến 100 ký tự';
            }
            if (!Number.isInteger(this.form.price) || this.form.price <= 0) {
                return 'Đơn giá phải là số nguyên lớn hơn 0';
            }
            if (!Number.isInteger(this.form.stock) || this.form.stock < 0) {
                return 'Số lượng tồn phải là số nguyên không âm';
            }
            return '';
        },
        async saveProduct() {
            if (this.saving) return;

            this.formError = this.validateForm();
            if (this.formError) return;

            this.saving = true;
            const wasEditing = this.editingId !== null;

            try {
                const method = wasEditing ? 'PUT' : 'POST';
                const url = wasEditing ? `${API_URL}?id=${this.editingId}` : API_URL;

                let response;
                try {
                    response = await fetch(url, {
                        method,
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            name: this.form.name,
                            price: this.form.price,
                            stock: this.form.stock
                        })
                    });
                } catch {
                    this.formError = 'Không thể kết nối API';
                    return;
                }

                let result;
                try {
                    result = await response.json();
                } catch {
                    this.formError = 'Phản hồi từ API không hợp lệ';
                    return;
                }

                if (!response.ok || !result.success) {
                    this.formError = result.message || 'Có lỗi xảy ra';
                    return;
                }

                this.showMessage(result.message, 'success');
                this.resetForm();
                await this.loadProducts(wasEditing ? this.pagination.page : 1);
            } finally {
                this.saving = false;
            }
        },
        async deleteProduct(product) {
            if (!confirm(`Bạn có chắc muốn xóa "${product.name}"?`)) return;

            try {
                const response = await fetch(`${API_URL}?id=${product.id}`, { method: 'DELETE' });
                const result = await response.json();

                if (!response.ok || !result.success) {
                    this.showMessage(result.message || 'Xóa sản phẩm thất bại', 'error');
                    return;
                }

                if (this.editingId === product.id) {
                    this.resetForm();
                }
                this.showMessage(result.message, 'success');
                await this.loadProducts(this.pagination.page);
            } catch {
                this.showMessage('Không thể kết nối API', 'error');
            }
        },
        goToPage(page) {
            if (this.loading || page < 1 || page > this.pagination.total_pages) return;
            this.loadProducts(page);
        }
    }
}).mount('#app');
