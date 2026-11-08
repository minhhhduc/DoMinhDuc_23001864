window.ProductCard = {
    name: 'ProductCard',
    props: {
        name: {
            type: String,
            required: true,
            validator: (value) => value.trim().length > 0
        },
        price: {
            type: Number,
            required: true,
            validator: (value) => Number.isFinite(value) && value >= 0
        },
        image: {
            type: String,
            default: DEFAULT_PRODUCT_IMAGE
        },
        description: {
            type: String,
            default: DEFAULT_PRODUCT_DESCRIPTION
        },
        stock: {
            type: Number,
            required: true,
            validator: (value) => Number.isInteger(value) && value >= 0
        }
    },
    data() {
        return {
            showDescription: false
        };
    },
    computed: {
        inStock() {
            return this.stock > 0;
        },
        displayImage() {
            return this.image || DEFAULT_PRODUCT_IMAGE;
        }
    },
    methods: {
        formatPrice(value) {
            return formatCurrency(value);
        },
        toggleDescription() {
            this.showDescription = !this.showDescription;
        },
        handleBuy() {
            this.$emit('buy', this.name);
        }
    },
    template: `
        <article class="product-card" :class="{ 'product-card--out': !inStock }">
            <div class="product-card__media">
                <img :src="displayImage" :alt="name">
                <span
                    class="product-card__status"
                    :class="inStock ? 'product-card__status--in' : 'product-card__status--out'"
                >
                    {{ inStock ? 'Còn hàng' : 'Hết hàng' }}
                </span>
            </div>

            <div class="product-card__body">
                <h2 class="product-card__name">{{ name }}</h2>
                <p class="product-card__price">{{ formatPrice(price) }}</p>
                <p class="product-card__stock">
                    Tồn kho: <strong>{{ stock }}</strong>
                </p>

                <p v-if="showDescription" class="product-card__description">{{ description }}</p>

                <div class="product-card__actions">
                    <button type="button" class="btn btn--ghost" @click="toggleDescription">
                        {{ showDescription ? 'Ẩn mô tả' : 'Xem mô tả' }}
                    </button>
                    <button
                        type="button"
                        class="btn btn--buy"
                        :disabled="!inStock"
                        :title="inStock ? 'Mua sản phẩm' : 'Sản phẩm đã hết hàng'"
                        @click="handleBuy"
                    >
                        Mua
                    </button>
                </div>
            </div>
        </article>
    `
};
