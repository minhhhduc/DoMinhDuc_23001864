const { createApp, ref, reactive } = Vue;

createApp({
    setup() {
        const API_URL = 'api/products.php';

        const products = ref([]);
        const message = ref('');
        const messageType = ref('success');
        const editingId = ref(null);
        const showForm = ref(false);
        const formError = ref('');
        const form = reactive({ name: '', price: null, stock: null });

        function showMessage(text, type = 'success') {
            message.value = text;
            messageType.value = type;
        }

        async function loadProducts() {
            try {
                const response = await fetch(API_URL);
                const result = await response.json();
                if (result.success) {
                    products.value = result.data;
                } else {
                    showMessage(result.message || 'Không tải được danh sách', 'error');
                }
            } catch {
                showMessage('Không thể kết nối API', 'error');
            }
        }

        function resetForm() {
            editingId.value = null;
            showForm.value = false;
            formError.value = '';
            form.name = '';
            form.price = null;
            form.stock = null;
        }

        function openAddForm() {
            resetForm();
            showForm.value = true;
        }

        function validateForm() {
            const name = (form.name || '').trim();
            if (name.length < 1 || name.length > 100) {
                return 'Tên sản phẩm phải từ 1 đến 100 ký tự';
            }
            if (!Number.isInteger(form.price) || form.price <= 0) {
                return 'Đơn giá phải là số nguyên lớn hơn 0';
            }
            if (!Number.isInteger(form.stock) || form.stock < 0) {
                return 'Số lượng tồn phải là số nguyên không âm';
            }
            return '';
        }

        async function saveProduct() {
            formError.value = validateForm();
            if (formError.value) return;

            try {
                const method = editingId.value ? 'PUT' : 'POST';
                const url = editingId.value ? `${API_URL}?id=${editingId.value}` : API_URL;
                const response = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name: form.name, price: form.price, stock: form.stock })
                });
                const result = await response.json();
                if (result.success) {
                    showMessage(result.message, 'success');
                    resetForm();
                    await loadProducts();
                } else {
                    formError.value = result.message || 'Có lỗi xảy ra';
                }
            } catch {
                formError.value = 'Không thể kết nối API';
            }
        }

        function editProduct(product) {
            editingId.value = product.id;
            showForm.value = true;
            form.name = product.name;
            form.price = product.price;
            form.stock = product.stock;
            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        }

        async function deleteProduct(product) {
            if (!confirm(`Bạn có chắc muốn xóa "${product.name}"?`)) return;
            try {
                const response = await fetch(`${API_URL}?id=${product.id}`, { method: 'DELETE' });
                const result = await response.json();
                if (result.success) {
                    showMessage(result.message, 'success');
                    if (editingId.value === product.id) resetForm();
                    await loadProducts();
                } else {
                    showMessage(result.message || 'Xóa thất bại', 'error');
                }
            } catch {
                showMessage('Không thể kết nối API', 'error');
            }
        }

        loadProducts();

        return { products, message, messageType, editingId, showForm, formError, form, saveProduct, editProduct, deleteProduct, resetForm, openAddForm };
    }
}).mount('#app');
