const { createApp } = Vue;

const API_URL = 'api/products.php';
const UPLOAD_URL = 'api/upload.php';
const MAX_IMAGE_SIZE = 2 * 1024 * 1024;
const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png'];
const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png'];

createApp({
    data() {
        return {
            products: [],
            loading: false,
            saving: false,
            message: '',
            messageType: 'success',
            editingId: null,
            showForm: false,
            formError: '',
            form: { name: '', price: null, stock: null },
            selectedFile: null,
            previewUrl: '',
            currentImage: '',
            requestSequence: 0
        };
    },
    computed: {
        previewSrc() {
            return this.previewUrl || this.currentImage;
        }
    },
    watch: {
        showForm(value) {
            document.body.style.overflow = value ? 'hidden' : '';
        }
    },
    mounted() {
        this.loadProducts();
    },
    beforeUnmount() {
        this.revokePreview();
        document.body.style.overflow = '';
    },
    methods: {
        showMessage(text, type = 'success') {
            this.message = text;
            this.messageType = type;
        },
        revokePreview() {
            if (this.previewUrl) {
                URL.revokeObjectURL(this.previewUrl);
                this.previewUrl = '';
            }
        },
        setPreview(file) {
            this.revokePreview();
            if (file) {
                this.previewUrl = URL.createObjectURL(file);
            }
        },
        clearFileInput() {
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },
        clearSelectedImage() {
            this.selectedFile = null;
            this.clearFileInput();
            this.setPreview(null);
        },
        async loadProducts() {
            const sequence = ++this.requestSequence;
            this.loading = true;

            try {
                const response = await fetch(API_URL);
                const result = await response.json();

                if (sequence !== this.requestSequence) return;

                if (!response.ok || !result.success) {
                    this.showMessage(result.message || 'Không tải được danh sách sản phẩm', 'error');
                    return;
                }

                this.products = result.data;
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
            this.currentImage = '';
            this.clearSelectedImage();
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
            this.currentImage = product.image || '';
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
        validateImageFile(file) {
            const extension = (file.name.split('.').pop() || '').toLowerCase();
            if (!ALLOWED_MIME_TYPES.includes(file.type) || !ALLOWED_EXTENSIONS.includes(extension)) {
                return 'Ảnh phải có định dạng JPG, JPEG hoặc PNG';
            }
            if (file.size > MAX_IMAGE_SIZE) {
                return 'Ảnh không được vượt quá 2 MB';
            }
            return '';
        },
        onFileChange(event) {
            this.formError = '';
            const file = event.target.files && event.target.files[0];

            if (!file) {
                this.clearSelectedImage();
                return;
            }

            const error = this.validateImageFile(file);
            if (error) {
                this.selectedFile = null;
                event.target.value = '';
                this.setPreview(null);
                this.formError = error;
                return;
            }

            this.selectedFile = file;
            this.setPreview(file);
        },
        async uploadImage(productId) {
            if (!this.selectedFile) return '';

            const data = new FormData();
            data.append('product_id', String(productId));
            data.append('image', this.selectedFile);

            try {
                const response = await fetch(UPLOAD_URL, { method: 'POST', body: data });
                const result = await response.json();

                if (!response.ok || !result.success) {
                    return result.message || 'Upload ảnh thất bại';
                }
                return '';
            } catch {
                return 'Không thể kết nối API upload';
            }
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

                if (!wasEditing) {
                    this.editingId = result.id;
                }

                const uploadError = this.selectedFile ? await this.uploadImage(this.editingId) : '';

                if (uploadError) {
                    const savedMessage = wasEditing
                        ? 'Đã cập nhật thông tin sản phẩm'
                        : 'Đã thêm sản phẩm';
                    this.formError = `${savedMessage} nhưng upload ảnh thất bại: ${uploadError}. Ảnh cũ (nếu có) vẫn được giữ. Vui lòng bấm Lưu để thử lại.`;
                    this.showMessage('Sản phẩm đã được lưu, nhưng ảnh chưa tải lên được', 'error');
                    await this.loadProducts();
                    return;
                }

                this.showMessage(wasEditing ? result.message : 'Đã thêm sản phẩm', 'success');
                this.resetForm();
                await this.loadProducts();
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
                await this.loadProducts();
            } catch {
                this.showMessage('Không thể kết nối API', 'error');
            }
        }
    }
}).mount('#app');
