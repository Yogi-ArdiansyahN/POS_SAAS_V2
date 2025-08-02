<?= $this->include('kasir/layouts/header') ?>
<!-- Main Content -->
<div class="container-fluid mt-3">
    <div class="row">
        <!-- Product Categories and Items (Left Side) -->
        <div class="col-lg-8 col-md-7 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-0">Menu</h5>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="searchProduct" placeholder="Cari Menu....">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Categories -->
                    <div class="categories-container mb-3">
                        <div class="d-flex flex-wrap">
                            <button class="btn btn-outline-primary me-2 mb-2 category-btn active" data-category="all">Semua</button>
                            <button class="btn btn-outline-primary me-2 mb-2 category-btn" data-category="food">Makanan</button>
                            <button class="btn btn-outline-primary me-2 mb-2 category-btn" data-category="drink">Minuman</button>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="row" id="productsContainer">
                        <!-- Products will be dynamically added here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart and Payment (Right Side) -->
        <div class="col-lg-4 col-md-5">
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- <h5 class="mb-0">Current Transaction</h5> -->
                        <h5 class="mb-0">Keranjang Transaksi</h5>
                        <button class="btn btn-sm btn-outline-danger" id="clearCart">
                            <i class="bi bi-trash"></i> Clear
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cartItems">
                                <!-- Cart items will be added here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Diskon :</span>
                        <span id="diskon">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>PPN (10%) :</span>
                        <span id="pajak">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total:</span>
                        <span id="total">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Metode Pembayaran</label>
                        <select class="form-select" id="paymentMethod">
                            <option value="cash">Tunai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="voucherCode" class="form-label">Kode Voucher</label>
                        <input type="text" class="form-control" id="voucherCode" placeholder="Masukkan kode voucher">
                        <div id="voucherFeedback" class="form-text text-danger"></div>
                    </div>

                    <div id="cashPaymentSection">
                        <div class="mb-3">
                            <label for="amountPaid" class="form-label">Jumlah yang Dibayar</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="amountPaid">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="change" class="form-label">Kembalian</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="change" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-success" id="processPayment">
                            <i class="bi bi-cash"></i> Proses Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<iframe id="pdfIframe" style="display:none;"></iframe>

<?= $this->include('kasir/layouts/footer') ?>


<script>
    $(document).ready(function() {
        const products = <?= $menus ?>;
        const diskons = <?= $diskons ?>;
        const pajak = 10; // Pajak 10%

        let cart = [];
        let appliedDiscount = null;

        displayProducts("all");

        $(".category-btn").click(function() {
            $(".category-btn").removeClass("active");
            $(this).addClass("active");
            const category = $(this).data("category");
            displayProducts(category);
        });

        $("#searchProduct").on("input", function() {
            const searchTerm = $(this).val().toLowerCase();
            const activeCategory = $(".category-btn.active").data("category");

            const filteredProducts = products.filter(product => {
                if (activeCategory !== "all" && product.category !== activeCategory) {
                    return false;
                }
                return product.name.toLowerCase().includes(searchTerm);
            });

            renderProducts(filteredProducts);
        });

        function displayProducts(category) {
            const filteredProducts = category === "all" ?
                products :
                products.filter(product => product.category === category);

            renderProducts(filteredProducts);
        }

        function renderProducts(productsToRender) {
            const productsContainer = $("#productsContainer");
            productsContainer.empty();

            productsToRender.forEach(product => {
                const isDisabled = product.current_quantity <= 0 ? 'pointer-events-none opacity-50' : '';

                const productCard = `
                <div class="col-lg-3 col-md-4 col-6 mb-3">
                    <div class="card product-card ${isDisabled}" data-id="${product.id}" data-quantity="${product.current_quantity}">
                        <img src="${product.image}" class="card-img-top product-img" alt="${product.name}">
                        <div class="card-body p-2">
                            <h6 class="card-title">${product.name}</h6>
                            <p class="card-text text-${product.current_quantity > 0 ? 'success' : 'danger'} mb-0">Stok ${product.current_quantity}</p>
                            <p class="card-text text-primary mb-0">Rp ${formatNumber(product.price)}</p>
                        </div>
                    </div>
                </div>
            `;

                productsContainer.append(productCard);
            });

            $(".product-card").click(function() {
                const quantity = $(this).data("quantity");
                if (quantity > 0) {
                    const productId = $(this).data("id");
                    addToCart(productId);
                }
            });
        }

        function addToCart(productId) {
            const product = products.find(p => p.id === productId);
            if (!product) return;

            const existingItem = cart.find(item => item.id === productId);
            if (existingItem) {
                if (existingItem.quantity >= product.current_quantity) {
                    Swal.fire({
                        position: "top-center",
                        icon: "info",
                        title: `Stok ${product.name} hanya tersedia ${product.current_quantity}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    return;
                }

                existingItem.quantity += 1;
                existingItem.total = existingItem.price * existingItem.quantity;
            } else {
                if (product.current_quantity < 1) {
                    alert(`${product.name} sedang habis stok`);
                    return;
                }

                cart.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    quantity: 1,
                    total: product.price
                });
            }

            updateCart();
        }

        function updateCart() {
            const cartContainer = $("#cartItems");
            cartContainer.empty();

            let subtotal = 0;

            cart.forEach(item => {
                subtotal += item.total;
                const cartItem = `
                <tr>
                    <td>${item.name}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-secondary qty-btn minus-btn" data-id="${item.id}">-</button>
                            <input type="text" class="form-control mx-1 cart-item-qty" value="${item.quantity}" readonly>
                            <button class="btn btn-sm btn-outline-secondary qty-btn plus-btn" data-id="${item.id}">+</button>
                        </div>
                    </td>
                    <td>Rp ${formatNumber(item.price)}</td>
                    <td>Rp ${formatNumber(item.total)}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger remove-item" data-id="${item.id}">
                            <i class="bi bi-x"></i>
                        </button>
                    </td>
                </tr>
            `;
                cartContainer.append(cartItem);
            });

            $(".minus-btn").click(function() {
                const itemId = $(this).data("id");
                decreaseQuantity(itemId);
            });

            $(".plus-btn").click(function() {
                const itemId = $(this).data("id");
                increaseQuantity(itemId);
            });

            $(".remove-item").click(function() {
                const itemId = $(this).data("id");
                removeItem(itemId);
            });

            // === Pajak dan Diskon ===
            let discount = 0;
            if (appliedDiscount) {
                discount = appliedDiscount.type === 'nominal' ?
                    appliedDiscount.value :
                    subtotal * (appliedDiscount.value / 100);
            }

            const dpp = subtotal - discount;
            const pajakValue = dpp * (pajak / 100);
            const total = dpp + pajakValue;

            $("#subtotal").text(`Rp ${formatNumber(subtotal)}`);
            $("#diskon").text(discount > 0 ? `-Rp ${formatNumber(discount)}` : `0`);
            $("#pajak").text(`Rp ${formatNumber(pajakValue)}`);
            $("#total").text(`Rp ${formatNumber(total)}`);

            calculateChange();
        }

        function increaseQuantity(itemId) {
            const item = cart.find(item => item.id === itemId);
            const product = products.find(p => p.id === itemId);
            if (!item || !product) return;

            if (item.quantity >= product.current_quantity) {
                Swal.fire({
                    position: "top-center",
                    icon: "info",
                    title: `Stok ${product.name} hanya tersedia ${product.current_quantity}`,
                    showConfirmButton: false,
                    timer: 1500
                });
                return;
            }

            item.quantity += 1;
            item.total = item.price * item.quantity;
            updateCart();
        }

        function decreaseQuantity(itemId) {
            const item = cart.find(item => item.id === itemId);
            if (item.quantity > 1) {
                item.quantity -= 1;
                item.total = item.price * item.quantity;
            } else {
                removeItem(itemId);
            }
            updateCart();
        }

        function removeItem(itemId) {
            cart = cart.filter(item => item.id !== itemId);
            updateCart();
        }

        $("#clearCart").click(function() {
            cart = [];
            updateCart();
        });

        $("#paymentMethod").change(function() {
            const method = $(this).val();
            $("#cashPaymentSection").toggle(method === "cash");
        });

        $("#amountPaid").on("input", function() {
            calculateChange();
        });

        $("#voucherCode").on("input", function() {
            const code = $(this).val().trim().toUpperCase();
            const match = diskons.find(d => d.kode.toUpperCase() === code);
            if (match) {
                appliedDiscount = match;
                $("#voucherFeedback").text(`Diskon diterapkan: ${match.type === 'nominal' ? 'Rp ' + formatNumber(match.value) : match.value + '%'} (${match.kode})`).removeClass("text-danger").addClass("text-success");
            } else {
                appliedDiscount = null;
                $("#voucherFeedback").text("Kode voucher tidak ditemukan").removeClass("text-success").addClass("text-danger");
            }
            updateCart();
        });

        function calculateChange() {
            const totalAmount = parseFloat($("#total").text().replace("Rp ", "").replace(/,/g, ""));
            const amountPaid = parseFloat($("#amountPaid").val()) || 0;
            const change = amountPaid - totalAmount;
            $("#change").val(change >= 0 ? formatNumber(change) : "Tidak Cukup.");
        }

        $("#processPayment").click(function() {
            if (cart.length === 0) {
                Swal.fire({
                    position: "top-center",
                    icon: "info",
                    title: "Keranjang kosong. Silakan tambahkan item untuk melanjutkan.",
                    showConfirmButton: false,
                    timer: 1500
                });
                return;
            }

            const paymentMethod = $("#paymentMethod").val();
            if (paymentMethod === "cash") {
                const amountPaid = parseFloat($("#amountPaid").val()) || 0;
                const totalAmount = parseFloat($("#total").text().replace("Rp ", "").replace(/,/g, ""));
                if (amountPaid < totalAmount) {
                    Swal.fire({
                        position: "top-center",
                        icon: "info",
                        title: "Jumlah yang dibayarkan tidak mencukupi.",
                        showConfirmButton: false,
                        timer: 1500
                    });
                    return;
                }
            }

            $.ajax({
                url: '<?= base_url('kasir/create'); ?>',
                type: 'GET',
                data: {
                    cart: cart,
                    diskon: appliedDiscount && appliedDiscount.kode ? appliedDiscount.kode : null
                },
                success: function(response) {
                    if (response.status === "success") {
                        generateReceipt(response.order);
                        const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'), {
                            backdrop: 'static',
                            keyboard: false
                        });
                        receiptModal.show();

                        cart = [];
                        updateCart();
                        $("#amountPaid").val("");
                        $("#change").val("");

                        document.getElementById('btnCloseReceiptModal').addEventListener('click', function() {
                            location.reload();
                        });
                    }
                },
                error: function() {
                    console.log('Error');
                }
            });
        });

        function generateReceipt(order) {
            const receiptContent = $("#receiptContent");
            const date = new Date();
            const transactionId = order;

            let subtotal = 0;
            cart.forEach(item => subtotal += item.total);

            let discount = 0;
            if (appliedDiscount) {
                discount = appliedDiscount.type === 'nominal' ?
                    appliedDiscount.value :
                    subtotal * (appliedDiscount.value / 100);
            }

            const dpp = subtotal - discount;
            const pajakValue = dpp * (pajak / 100);
            const total = dpp + pajakValue;
            const paymentMethod = $("#paymentMethod").val();
            const amountPaid = parseFloat($("#amountPaid").val()) || 0;
            const change = amountPaid - total;

            let receiptHTML = `
            <div class="receipt-header">
                <h4>POS SYSTEM</h4>
                <p>Kuintansi Transaksi</p>
                <p>${date.toLocaleDateString()} ${date.toLocaleTimeString()}</p>
                <p>Transaksi ID: ${transactionId}</p>
            </div>
            <div class="receipt-items">
                <div class="receipt-item">
                    <span><strong>Item</strong></span>
                    <span><strong>Jumlah</strong></span>
                    <span><strong>Harga</strong></span>
                    <span><strong>Total</strong></span>
                </div>
                <hr>`;

            cart.forEach(item => {
                receiptHTML += `
                <div class="receipt-item">
                    <span>${item.name}</span>
                    <span>${item.quantity}</span>
                    <span>Rp ${formatNumber(item.price)}</span>
                    <span>Rp ${formatNumber(item.total)}</span>
                </div>`;
            });

            receiptHTML += `
            <hr>
            <div class="receipt-item">
                <span>Subtotal:</span><span></span><span></span>
                <span>Rp ${formatNumber(subtotal)}</span>
            </div>
            ${discount > 0 ? `
            <div class="receipt-item">
                <span>Diskon (${appliedDiscount.kode})</span><span></span><span></span>
                <span>- Rp ${formatNumber(discount)}</span>
            </div>` : ''}
            <div class="receipt-item">
                <span>Pajak (${pajak}%):</span><span></span><span></span>
                <span>Rp ${formatNumber(pajakValue)}</span>
            </div>
            <div class="receipt-item receipt-total">
                <span>Total:</span><span></span><span></span>
                <span>Rp ${formatNumber(total)}</span>
            </div>`;

            if (paymentMethod === "cash") {
                receiptHTML += `
                <div class="receipt-item">
                    <span>Jumlah yang Dibayar:</span><span></span><span></span>
                    <span>Rp ${formatNumber(amountPaid)}</span>
                </div>
                <div class="receipt-item">
                    <span>Kembalian:</span><span></span><span></span>
                    <span>Rp ${formatNumber(change)}</span>
                </div>`;
            }

            receiptHTML += `
            <hr>
            <div class="receipt-item">
                <span>Metode Pembayaran:</span><span></span><span></span>
                <span>${paymentMethod.toUpperCase()}</span>
            </div>
            <div class="text-center mt-3">
                <p>Terimakasih.</p>
            </div>`;

            receiptContent.html(receiptHTML);
        }

        $("#printReceipt").click(function() {
            const receiptContent = document.getElementById("receiptContent").innerHTML;
            const printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Receipt</title>');
            printWindow.document.write('<style>body { font-family: "Courier New", Courier, monospace; font-size: 12px; }</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(receiptContent);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        });

        function formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>