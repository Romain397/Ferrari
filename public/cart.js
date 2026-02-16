(function () {
    const storageKey = "ferrari_cart";
    const body = document.body;
    const isAuthenticated = body?.dataset.userAuthenticated === "1";
    const sessionStore = window.sessionStorage;
    const localStore = window.localStorage;
    const activeStore = isAuthenticated ? localStore : sessionStore;
    const countElement = document.getElementById("cart-count");

    const parseCart = (value) => {
        try {
            const parsed = JSON.parse(value || "[]");
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    };

    const normalizeCart = (cart) =>
        cart
            .filter((item) => item && Number(item.id) > 0)
            .map((item) => ({
                id: Number(item.id),
                name: String(item.name || ""),
                price: Number(item.price || 0),
                image: String(item.image || ""),
                quantity: Math.max(1, Number(item.quantity || 1)),
            }));

    const readFrom = (store) => normalizeCart(parseCart(store.getItem(storageKey)));
    const readCart = () => readFrom(activeStore);
    const saveCart = (cart) => activeStore.setItem(storageKey, JSON.stringify(normalizeCart(cart)));

    const mergeCarts = (first, second) => {
        const merged = [...first];

        second.forEach((item) => {
            const existing = merged.find((entry) => entry.id === item.id);
            if (existing) {
                existing.quantity += item.quantity;
                if (!existing.image && item.image) existing.image = item.image;
                if (!existing.name && item.name) existing.name = item.name;
                if (!existing.price && item.price) existing.price = item.price;
                return;
            }
            merged.push(item);
        });

        return normalizeCart(merged);
    };

    const syncStorageByAuth = () => {
        const localCart = readFrom(localStore);
        const sessionCart = readFrom(sessionStore);

        if (isAuthenticated) {
            const merged = mergeCarts(localCart, sessionCart);
            localStore.setItem(storageKey, JSON.stringify(merged));
            sessionStore.removeItem(storageKey);
            return;
        }

        if (sessionCart.length === 0 && localCart.length > 0) {
            sessionStore.setItem(storageKey, JSON.stringify(localCart));
        }
        localStore.removeItem(storageKey);
    };

    const getCount = (cart) => cart.reduce((total, item) => total + item.quantity, 0);
    const formatPrice = (value) => `${value.toFixed(2)} €`;
    const escapeHtml = (value) =>
        String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");

    const updateCartBadge = () => {
        if (!countElement) return;
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
        renderAll();
    };

    const updateItemQuantity = (productId, quantity) => {
        const cart = readCart();
        const item = cart.find((entry) => entry.id === productId);
        if (!item) return;

        if (quantity <= 0) {
            saveCart(cart.filter((entry) => entry.id !== productId));
            return;
        }

        item.quantity = quantity;
        saveCart(cart);
    };

    const removeItem = (productId) => {
        saveCart(readCart().filter((entry) => entry.id !== productId));
    };

    const buildStoreCartItem = (item) => {
        const safeName = escapeHtml(item.name);
        const lineTotal = Number(item.price || 0) * Number(item.quantity || 0);
        const imageHtml = item.image
            ? `<img src="${item.image}" alt="${safeName}" class="store-cart-item-img rounded">`
            : `<div class="store-cart-item-img rounded d-flex align-items-center justify-content-center bg-secondary-subtle text-dark">N/A</div>`;

        return `
            <div class="store-cart-item" data-product-id="${item.id}">
                <div class="d-flex gap-2 align-items-center">
                    ${imageHtml}
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">${safeName}</div>
                        <div class="text-danger small">${formatPrice(lineTotal)}</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger js-cart-remove">x</button>
                </div>
                <div class="input-group input-group-sm mt-2 store-cart-qty-group">
                    <button class="btn btn-outline-light js-cart-dec" type="button">-</button>
                    <input type="number" class="form-control text-center js-cart-qty" min="1" value="${item.quantity}">
                    <button class="btn btn-outline-light js-cart-inc" type="button">+</button>
                </div>
            </div>
        `;
    };

    const renderStoreMiniCart = (cart) => {
        const panel = document.getElementById("store-cart-panel");
        if (!panel) return;

        if (panel.dataset.ordered === "1") {
            activeStore.removeItem(storageKey);
            cart = [];
        }

        const emptyElement = document.getElementById("store-cart-empty");
        const contentElement = document.getElementById("store-cart-content");
        const itemsElement = document.getElementById("store-cart-items");
        const totalElement = document.getElementById("store-cart-total");
        const itemsInput = document.getElementById("store-cart-items-input");
        if (!emptyElement || !contentElement || !itemsElement || !totalElement || !itemsInput) return;

        if (cart.length === 0) {
            emptyElement.style.display = "block";
            contentElement.style.display = "none";
            itemsInput.value = "[]";
            return;
        }

        emptyElement.style.display = "none";
        contentElement.style.display = "block";
        itemsElement.innerHTML = cart.map(buildStoreCartItem).join("");

        const total = cart.reduce(
            (sum, item) => sum + Number(item.price || 0) * Number(item.quantity || 0),
            0
        );
        totalElement.textContent = formatPrice(total);
        itemsInput.value = JSON.stringify(cart);
    };

    const renderLegacyCartPage = (cart) => {
        const cartPage = document.getElementById("cart-page");
        if (!cartPage) return;

        if (cartPage.dataset.ordered === "1") {
            activeStore.removeItem(storageKey);
            cart = [];
        }

        const emptyElement = document.getElementById("cart-empty");
        const contentElement = document.getElementById("cart-content");
        const itemsElement = document.getElementById("cart-items");
        const totalElement = document.getElementById("cart-total");
        const itemsInput = document.getElementById("cart-items-input");
        if (!emptyElement || !contentElement || !itemsElement || !totalElement || !itemsInput) return;

        if (cart.length === 0) {
            emptyElement.style.display = "block";
            contentElement.style.display = "none";
            itemsInput.value = "[]";
            return;
        }

        emptyElement.style.display = "none";
        contentElement.style.display = "block";
        itemsElement.innerHTML = cart
            .map((item) => {
                const safeName = escapeHtml(item.name);
                const lineTotal = Number(item.price || 0) * Number(item.quantity || 0);
                const imageHtml = item.image
                    ? `<img src="${item.image}" alt="${safeName}" class="cart-product-img rounded">`
                    : `<div class="cart-product-img rounded d-flex align-items-center justify-content-center bg-secondary-subtle text-dark">N/A</div>`;

                return `
                    <tr data-product-id="${item.id}">
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                ${imageHtml}
                                <strong>${safeName}</strong>
                            </div>
                        </td>
                        <td>${formatPrice(Number(item.price || 0))}</td>
                        <td>
                            <div class="input-group input-group-sm cart-qty-group">
                                <button class="btn btn-outline-light js-cart-dec" type="button">-</button>
                                <input type="number" class="form-control text-center js-cart-qty" min="1" value="${item.quantity}">
                                <button class="btn btn-outline-light js-cart-inc" type="button">+</button>
                            </div>
                        </td>
                        <td>${formatPrice(lineTotal)}</td>
                        <td><button type="button" class="btn btn-sm btn-danger js-cart-remove">Supprimer</button></td>
                    </tr>
                `;
            })
            .join("");

        const total = cart.reduce(
            (sum, item) => sum + Number(item.price || 0) * Number(item.quantity || 0),
            0
        );
        totalElement.textContent = formatPrice(total);
        itemsInput.value = JSON.stringify(cart);
    };

    const renderAll = () => {
        const cart = readCart();
        renderStoreMiniCart(cart);
        renderLegacyCartPage(cart);
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
                if (!id || !name || Number.isNaN(price)) return;
                addToCart({ id, name, price, image });
            });
        });
    };

    const handleCartAction = (root) => {
        if (!root) return;

        root.addEventListener("click", (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) return;

            const row = target.closest("[data-product-id]");
            if (!(row instanceof HTMLElement)) return;

            const productId = parseInt(row.dataset.productId || "0", 10);
            if (!productId) return;

            if (target.classList.contains("js-cart-remove")) {
                removeItem(productId);
                renderAll();
                return;
            }

            if (target.classList.contains("js-cart-inc") || target.classList.contains("js-cart-dec")) {
                const input = row.querySelector(".js-cart-qty");
                if (!(input instanceof HTMLInputElement)) return;
                const current = parseInt(input.value || "1", 10);
                const next = target.classList.contains("js-cart-inc") ? current + 1 : current - 1;
                updateItemQuantity(productId, next);
                renderAll();
            }
        });

        root.addEventListener("change", (event) => {
            const target = event.target;
            if (!(target instanceof HTMLInputElement) || !target.classList.contains("js-cart-qty")) return;

            const row = target.closest("[data-product-id]");
            if (!(row instanceof HTMLElement)) return;

            const productId = parseInt(row.dataset.productId || "0", 10);
            if (!productId) return;

            updateItemQuantity(productId, parseInt(target.value || "1", 10));
            renderAll();
        });
    };

    syncStorageByAuth();
    initStoreButtons();
    handleCartAction(document.getElementById("store-cart-panel"));
    handleCartAction(document.getElementById("cart-page"));
    renderAll();
})();
