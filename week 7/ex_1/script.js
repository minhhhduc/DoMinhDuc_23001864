const { createApp } = Vue;

const CATEGORIES = ['Điện thoại', 'Laptop', 'Phụ kiện'];

const currencyFormatter = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0
});

createApp({
    data() {
        return {
            status: 'Đang khởi tạo...',
            keyword: '',
            keywordWarning: '',
            selectedCategory: '',
            sortOrder: '',
            categories: CATEGORIES,
            products: [
                { id: 1, name: 'iPhone 15 Pro Max', price: 34990000, category: 'Điện thoại', stock: 6 },
                { id: 2, name: 'Samsung Galaxy S24', price: 22490000, category: 'Điện thoại', stock: 0 },
                { id: 3, name: 'MacBook Air M3', price: 27990000, category: 'Laptop', stock: 5 },
                { id: 4, name: 'Dell XPS 13 Plus', price: 31990000, category: 'Laptop', stock: 3 },
                { id: 5, name: 'Bàn phím cơ Keychron K8', price: 1890000, category: 'Phụ kiện', stock: 14 },
                { id: 6, name: 'Chuột Logitech MX Master 3S', price: 2490000, category: 'Phụ kiện', stock: 9 },
                { id: 7, name: 'Tai nghe Sony WH-1000XM5', price: 7490000, category: 'Phụ kiện', stock: 0 }
            ],
            newProduct: {
                name: '',
                price: '',
                category: CATEGORIES[0],
                stock: ''
            },
            formErrors: []
        };
    },
    computed: {
        filteredProducts() {
            const keyword = this.keyword.trim().toLowerCase();

            return this.products.filter((product) => {
                const matchesKeyword = !keyword || product.name.toLowerCase().includes(keyword);
                const matchesCategory = !this.selectedCategory || product.category === this.selectedCategory;

                return matchesKeyword && matchesCategory;
            });
        },
        visibleProducts() {
            const products = this.filteredProducts.slice();

            if (this.sortOrder === 'asc') {
                products.sort((a, b) => a.price - b.price);
            } else if (this.sortOrder === 'desc') {
                products.sort((a, b) => b.price - a.price);
            }

            return products;
        },
        visibleCount() {
            return this.visibleProducts.length;
        },
        totalInventoryValue() {
            return this.visibleProducts.reduce(
                (total, product) => total + product.price * product.stock,
                0
            );
        }
    },
    watch: {
        keyword(newKeyword) {
            this.keywordWarning = newKeyword.length === 1
                ? 'Từ khóa chỉ có 1 ký tự. Hãy nhập thêm ký tự để tìm kiếm chính xác hơn.'
                : '';
        }
    },
    mounted() {
        this.status = 'Sẵn sàng';
    },
    methods: {
        formatCurrency(value) {
            return currencyFormatter.format(Number(value) || 0);
        },
        resetFilters() {
            this.keyword = '';
            this.selectedCategory = '';
            this.sortOrder = '';
        },
        addProduct() {
            const errors = [];
            const name = this.newProduct.name.trim();
            const price = Number(this.newProduct.price);
            const stock = Number(this.newProduct.stock);

            if (!name) {
                errors.push('Tên sản phẩm không được để trống.');
            }

            if (
                this.newProduct.price === '' ||
                this.newProduct.price === null ||
                !Number.isFinite(price) ||
                price <= 0
            ) {
                errors.push('Giá sản phẩm phải là số lớn hơn 0.');
            }

            if (
                this.newProduct.stock === '' ||
                this.newProduct.stock === null ||
                !Number.isInteger(stock) ||
                stock < 0
            ) {
                errors.push('Số lượng tồn kho phải là số nguyên không âm.');
            }

            this.formErrors = errors;

            if (errors.length > 0) {
                return;
            }

            const maxId = this.products.reduce((max, product) => Math.max(max, product.id), 0);

            this.products.push({
                id: maxId + 1,
                name,
                price,
                category: this.newProduct.category,
                stock
            });

            this.newProduct = {
                name: '',
                price: '',
                category: CATEGORIES[0],
                stock: ''
            };
        },
        removeProduct(product) {
            const confirmed = window.confirm(`Bạn có chắc muốn xóa sản phẩm "${product.name}"?`);

            if (confirmed) {
                this.products = this.products.filter((item) => item.id !== product.id);
            }
        }
    }
}).mount('#app');
