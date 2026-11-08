window.CartItem = {
    name: 'CartItem',
    props: {
        item: {
            type: Object,
            required: true,
            validator: (value) => (
                value !== null &&
                typeof value === 'object' &&
                Number.isInteger(value.id) &&
                typeof value.name === 'string' &&
                value.name.trim().length > 0 &&
                Number.isFinite(value.unitPrice) &&
                value.unitPrice >= 0 &&
                Number.isInteger(value.quantity) &&
                value.quantity >= CART_QUANTITY_MIN &&
                value.quantity <= CART_QUANTITY_MAX
            )
        }
    },
    emits: ['doi-so-luong', 'yeu-cau-xoa'],
    computed: {
        lineTotal() {
            return this.item.unitPrice * this.item.quantity;
        },
        canDecrease() {
            return this.item.quantity > CART_QUANTITY_MIN;
        },
        canIncrease() {
            return this.item.quantity < CART_QUANTITY_MAX;
        }
    },
    methods: {
        formatPrice(value) {
            return formatCurrency(value);
        },
        decreaseQuantity() {
            if (!this.canDecrease) {
                return;
            }

            this.$emit('doi-so-luong', this.item.id, this.item.quantity - 1);
        },
        increaseQuantity() {
            if (!this.canIncrease) {
                return;
            }

            this.$emit('doi-so-luong', this.item.id, this.item.quantity + 1);
        },
        removeItem() {
            this.$emit('yeu-cau-xoa', this.item.id);
        }
    },
    template: `
        <article class="cart-item">
            <div class="cart-item__info">
                <p class="cart-item__id">Mã #{{ item.id }}</p>
                <h2 class="cart-item__name">{{ item.name }}</h2>
                <p class="cart-item__price">Đơn giá: {{ formatPrice(item.unitPrice) }}</p>
            </div>

            <div class="cart-item__quantity" role="group" aria-label="Điều chỉnh số lượng">
                <button
                    type="button"
                    class="qty-btn"
                    :disabled="!canDecrease"
                    aria-label="Giảm số lượng"
                    @click="decreaseQuantity"
                >
                    −
                </button>
                <span class="qty-value">{{ item.quantity }}</span>
                <button
                    type="button"
                    class="qty-btn"
                    :disabled="!canIncrease"
                    aria-label="Tăng số lượng"
                    @click="increaseQuantity"
                >
                    +
                </button>
            </div>

            <div class="cart-item__total">
                <span class="cart-item__total-label">Thành tiền</span>
                <strong>{{ formatPrice(lineTotal) }}</strong>
            </div>

            <button type="button" class="btn btn--danger" @click="removeItem">
                Xóa
            </button>
        </article>
    `
};
