(function () {
    const storageKey = "ferrari_cart";
    const countElement = document.getElementById("cart-count");

    const readCart = () => {
        try {
            const value = localStorage.getItem(storageKey);
            if (!value) {
                return [];
            }

            const parsed = JSON.parse(value);
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    };

    const saveCart = (cart) => {
        localStorage.setItem(storageKey, JSON.stringify(cart));
    };

    const getCount = (cart) => cart.reduce((total, item) => total + (item.quantity || 0), 0);

    const updateCartBadge = () => {
        if (!countElement) {
            return;
        }

        countElement.textContent = String(getCount(readCart()));
    };

    const addToCart = (product) => {
        const cart = readCart();
        const existing = cart.find((item) => item.id === product.id);

        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                quantity: 1,
            });
        }

        saveCart(cart);
        updateCartBadge();
    };

    const initStoreButtons = () => {
        const buttons = document.querySelectorAll(".js-add-to-cart");

        buttons.forEach((button) => {
            button.addEventListener("click", function () {
                const id = parseInt(this.dataset.productId || "0", 10);
                const name = this.dataset.productName || "";
                const price = parseFloat(this.dataset.productPrice || "0");
                const image = this.dataset.productImage || "";

                if (!id || !name || Number.isNaN(price)) {
                    return;
                }

                addToCart({ id, name, price, image });
            });
        });
    };

    const formatPrice = (value) => `${value.toFixed(2)} €`;

    const renderCartPage = () => {
        const cartPage = document.getElementById("cart-page");
        if (!cartPage) {
            return;
        }

        if (cartPage.dataset.ordered === "1") {
            localStorage.removeItem(storageKey);
        }

        const cart = readCart();
        const emptyElement = document.getElementById("cart-empty");
        const contentElement = document.getElementById("cart-content");
        const itemsElement = document.getElementById("cart-items");
        const totalElement = document.getElementById("cart-total");
        const cartItemsInput = document.getElementById("cart-items-input");

        if (!emptyElement || !contentElement || !itemsElement || !totalElement || !cartItemsInput) {
            return;
        }

        if (cart.length === 0) {
            emptyElement.style.display = "block";
            contentElement.style.display = "none";
            cartItemsInput.value = "[]";
            updateCartBadge();
            return;
        }

        emptyElement.style.display = "none";
        contentElement.style.display = "block";

        let total = 0;
        itemsElement.innerHTML = "";

        cart.forEach((item) => {
            const quantity = Number(item.quantity || 0);
            const price = Number(item.price || 0);
            const lineTotal = quantity * price;
            total += lineTotal;

            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${item.name}</td>
                <td>${formatPrice(price)}</td>
                <td>${quantity}</td>
                <td>${formatPrice(lineTotal)}</td>
            `;

            itemsElement.appendChild(row);
        });

        totalElement.textContent = formatPrice(total);
        cartItemsInput.value = JSON.stringify(cart);
        updateCartBadge();
    };

    updateCartBadge();
    initStoreButtons();
    renderCartPage();
})();
