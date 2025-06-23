<?php
include '../includes/config.php';
include '../includes/auth.php';
include '../includes/functions.php';

// Check if user is logged in and has shipping info
if (!isLoggedIn()) {
    header("Location: ../login.php?redirect=products/checkout.php");
    exit();
}

$cart_items = $_SESSION['cart'] ?? [];
if (empty($cart_items)) {
    header("Location: products.php");
    exit();
}

// Get shipping info from session
$shipping_info = json_decode($_SESSION['shipping_info'] ?? '{}', true);
if (empty($shipping_info)) {
    header("Location: order.php");
    exit();
}

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = 50.00;
$total = $subtotal + $shipping;

// Process payment if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate payment details
    $errors = [];
    
    // Get form data
    $card_number = str_replace(' ', '', $_POST['card_number'] ?? '');
    $card_name = trim($_POST['card_name'] ?? '');
    $card_month = $_POST['card_month'] ?? '';
    $card_year = $_POST['card_year'] ?? '';
    $card_cvv = $_POST['card_cvv'] ?? '';
    
    // Simple validation
    if (empty($card_number) || !preg_match('/^[0-9]{13,16}$/', $card_number)) {
        $errors[] = "Invalid card number";
    }
    
    if (empty($card_name)) {
        $errors[] = "Card holder name is required";
    }
    
    if (empty($card_month) || empty($card_year)) {
        $errors[] = "Expiration date is required";
    }
    
    if (empty($card_cvv) || !preg_match('/^[0-9]{3,4}$/', $card_cvv)) {
        $errors[] = "Invalid CVV";
    }
    
    // If no errors, process payment
    if (empty($errors)) {
        try {
            // Start transaction
            $db->begin_transaction();
            
            // 1. Create order record
            $stmt = $db->prepare("INSERT INTO orders (user_id, total_amount, shipping_amount, status, 
                                 shipping_name, shipping_address, shipping_city, shipping_province, 
                                 shipping_postal_code, shipping_phone, payment_method)
                                 VALUES (?, ?, ?, 'processing', ?, ?, ?, ?, ?, ?, 'credit_card')");
            $stmt->bind_param("iddssssss", 
                $_SESSION['user_id'],
                $total,
                $shipping,
                $shipping_info['first_name'] . ' ' . $shipping_info['last_name'],
                $shipping_info['address'],
                $shipping_info['city'],
                $shipping_info['province'],
                $shipping_info['postal_code'],
                $shipping_info['phone']
            );
            $stmt->execute();
            $order_id = $stmt->insert_id;
            $stmt->close();
            
            // 2. Add order items
            $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price)
                                 VALUES (?, ?, ?, ?, ?)");
            
            foreach ($cart_items as $product_id => $item) {
                $stmt->bind_param("issid", 
                    $order_id,
                    $product_id,
                    $item['name'],
                    $item['quantity'],
                    $item['price']
                );
                $stmt->execute();
                
                // 3. Update product stock (optional)
                // $db->query("UPDATE products SET stock = stock - {$item['quantity']} WHERE id = $product_id");
            }
            $stmt->close();
            
            // 4. Create payment record (in a real app, you'd process with a payment gateway first)
            $last4 = substr($card_number, -4);
            $stmt = $db->prepare("INSERT INTO payments (order_id, amount, payment_method, transaction_id, last4, status)
                                 VALUES (?, ?, 'credit_card', ?, ?, 'completed')");
            $transaction_id = 'PAY-' . strtoupper(uniqid());
            $stmt->bind_param("idss", $order_id, $total, $transaction_id, $last4);
            $stmt->execute();
            $stmt->close();
            
            // Commit transaction
            $db->commit();
            
            // Store order ID in session for confirmation page
            $_SESSION['last_order_id'] = $order_id;
            $_SESSION['order_total'] = $total;
            
            // Clear cart
            unset($_SESSION['cart']);
            
            // Redirect to confirmation
            header("Location: confirmation.php");
            exit();
            
        } catch (Exception $e) {
            $db->rollback();
            $errors[] = "Payment processing failed: " . $e->getMessage();
        }
    }
}

$page_title = "Checkout - " . SITE_NAME;
include '../includes/header.php';
?>

<!-- Display errors if any -->
<?php if (!empty($errors)): ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Payment Error!</strong>
        <ul class="mt-2">
            <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
            <div class="md:col-span-2">
                <form method="POST" id="paymentForm">
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-xl font-semibold mb-4">Payment Method</h3>
                        
                        <!-- Credit Card Form -->
                        <div class="wrapper" id="app">
                            <div class="card-form">
                                <div class="card-list">
                                    <div class="card-item" v-bind:class="{ '-active' : isCardFlipped }">
                                        <!-- Front of Card -->
                                        <div class="card-item__side -front">
                                            <div class="card-item__focus" v-bind:class="{'-active' : focusElementStyle }" v-bind:style="focusElementStyle" ref="focusElement"></div>
                                            <div class="card-item__cover">
                                                <img v-bind:src="'https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/' + currentCardBackground + '.jpeg'" class="card-item__bg">
                                            </div>
                                            
                                            <div class="card-item__wrapper">
                                                <div class="card-item__top">
                                                    <img src="https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/chip.png" class="card-item__chip">
                                                    <div class="card-item__type">
                                                        <transition name="slide-fade-up">
                                                            <img v-bind:src="'https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/' + getCardType + '.png'" v-if="getCardType" v-bind:key="getCardType" alt="" class="card-item__typeImg">
                                                        </transition>
                                                    </div>
                                                </div>
                                                
                                                <!-- Card Number Display -->
                                                <label for="cardNumber" class="card-item__number" ref="cardNumber">
                                                    <template v-if="getCardType === 'amex'">
                                                     <span v-for="(n, $index) in amexCardMask" :key="$index">
                                                      <transition name="slide-fade-up">
                                                        <div class="card-item__numberItem" v-if="$index > 4 && $index < 14 && cardNumber.length > $index && n.trim() !== ''">*</div>
                                                        <div class="card-item__numberItem" :class="{ '-active' : n.trim() === '' }" :key="$index" v-else-if="cardNumber.length > $index">{{cardNumber[$index]}}</div>
                                                        <div class="card-item__numberItem" :class="{ '-active' : n.trim() === '' }" v-else :key="$index + 1">{{n}}</div>
                                                      </transition>
                                                    </span>
                                                    </template>

                                                    <template v-else>
                                                      <span v-for="(n, $index) in otherCardMask" :key="$index">
                                                        <transition name="slide-fade-up">
                                                          <div class="card-item__numberItem" v-if="$index > 4 && $index < 15 && cardNumber.length > $index && n.trim() !== ''">*</div>
                                                          <div class="card-item__numberItem" :class="{ '-active' : n.trim() === '' }" :key="$index" v-else-if="cardNumber.length > $index">{{cardNumber[$index]}}</div>
                                                          <div class="card-item__numberItem" :class="{ '-active' : n.trim() === '' }" v-else :key="$index + 1">{{n}}</div>
                                                        </transition>
                                                      </span>
                                                    </template>
                                                </label>
                                                
                                                <div class="card-item__content">
                                                    <label for="cardName" class="card-item__info" ref="cardName">
                                                        <div class="card-item__holder">Card Holder</div>
                                                        <transition name="slide-fade-up">
                                                            <div class="card-item__name" v-if="cardName.length" key="1">
                                                                <transition-group name="slide-fade-right">
                                                                    <span class="card-item__nameItem" v-for="(n, $index) in cardName.replace(/\s\s+/g, ' ')" v-if="$index === $index" v-bind:key="$index + 1">{{n}}</span>
                                                                </transition-group>
                                                            </div>
                                                            <div class="card-item__name" v-else key="2">Full Name</div>
                                                        </transition>
                                                    </label>
                                                    <div class="card-item__date" ref="cardDate">
                                                        <label for="cardMonth" class="card-item__dateTitle">Expires</label>
                                                        <label for="cardMonth" class="card-item__dateItem">
                                                            <transition name="slide-fade-up">
                                                                <span v-if="cardMonth" v-bind:key="cardMonth">{{cardMonth}}</span>
                                                                <span v-else key="2">MM</span>
                                                            </transition>
                                                        </label>
                                                        /
                                                        <label for="cardYear" class="card-item__dateItem">
                                                            <transition name="slide-fade-up">
                                                                <span v-if="cardYear" v-bind:key="cardYear">{{String(cardYear).slice(2,4)}}</span>
                                                                <span v-else key="2">YY</span>
                                                            </transition>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Back of Card -->
                                        <div class="card-item__side -back">
                                            <div class="card-item__cover">
                                                <img v-bind:src="'https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/' + currentCardBackground + '.jpeg'" class="card-item__bg">
                                            </div>
                                            <div class="card-item__band"></div>
                                            <div class="card-item__cvv">
                                                <div class="card-item__cvvTitle">CVV</div>
                                                <div class="card-item__cvvBand">
                                                    <span v-for="(n, $index) in cardCvv" :key="$index">*</span>
                                                </div>
                                                <div class="card-item__type">
                                                    <img v-bind:src="'https://raw.githubusercontent.com/muhammederdem/credit-card-form/master/src/assets/images/' + getCardType + '.png'" v-if="getCardType" class="card-item__typeImg">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Payment Form -->
                                <div class="card-form__inner">
                                    <div class="card-input">
                                        <label for="cardNumber" class="card-input__label">Card Number</label>
                                        <input type="text" id="cardNumber" name="card_number" class="card-input__input" v-mask="generateCardNumberMask" v-model="cardNumber" v-on:focus="focusInput" v-on:blur="blurInput" data-ref="cardNumber" autocomplete="off" required>
                                    </div>
                                    <div class="card-input">
                                        <label for="cardName" class="card-input__label">Card Holder</label>
                                        <input type="text" id="cardName" name="card_name" class="card-input__input" v-model="cardName" v-on:focus="focusInput" v-on:blur="blurInput" data-ref="cardName" autocomplete="off" required>
                                    </div>
                                    <div class="card-form__row">
                                        <div class="card-form__col">
                                            <div class="card-form__group">
                                                <label for="cardMonth" class="card-input__label">Expiration Date</label>
                                                <select class="card-input__input -select" id="cardMonth" name="card_month" v-model="cardMonth" v-on:focus="focusInput" v-on:blur="blurInput" data-ref="cardDate" required>
                                                    <option value="" disabled selected>Month</option>
                                                    <option v-bind:value="n < 10 ? '0' + n : n" v-for="n in 12" v-bind:disabled="n < minCardMonth" v-bind:key="n">
                                                        {{n < 10 ? '0' + n : n}}
                                                    </option>
                                                </select>
                                                <select class="card-input__input -select" id="cardYear" name="card_year" v-model="cardYear" v-on:focus="focusInput" v-on:blur="blurInput" data-ref="cardDate" required>
                                                    <option value="" disabled selected>Year</option>
                                                    <option v-bind:value="$index + minCardYear" v-for="(n, $index) in 12" v-bind:key="n">
                                                        {{$index + minCardYear}}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="card-form__col -cvv">
                                            <div class="card-input">
                                                <label for="cardCvv" class="card-input__label">CVV</label>
                                                <input type="text" class="card-input__input" id="cardCvv" name="card_cvv" v-mask="'####'" maxlength="4" v-model="cardCvv" v-on:focus="flipCard(true)" v-on:blur="flipCard(false)" autocomplete="off" required>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="card-form__button">
                                        Pay R <?= number_format($total, 2) ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold mb-4">Shipping Information</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-700">Name:</p>
                            <p><?= htmlspecialchars($shipping_info['first_name'] . ' ' . $shipping_info['last_name']) ?></p>
                        </div>
                        <div>
                            <p class="text-gray-700">Phone:</p>
                            <p><?= htmlspecialchars($shipping_info['phone']) ?></p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-700">Address:</p>
                            <p><?= htmlspecialchars($shipping_info['address']) ?></p>
                        </div>
                        <div>
                            <p class="text-gray-700">City:</p>
                            <p><?= htmlspecialchars($shipping_info['city']) ?></p>
                        </div>
                        <div>
                            <p class="text-gray-700">Province:</p>
                            <p><?= htmlspecialchars($shipping_info['province']) ?></p>
                        </div>
                        <div>
                            <p class="text-gray-700">Postal Code:</p>
                            <p><?= htmlspecialchars($shipping_info['postal_code']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h3 class="text-xl font-semibold mb-4">Order Summary</h3>
                    <div class="space-y-4">
                        <?php foreach ($cart_items as $id => $item): ?>
                        <div class="flex justify-between">
                            <div>
                                <p><?= htmlspecialchars($item['name']) ?></p>
                                <p class="text-sm text-gray-500">Qty: <?= (int)$item['quantity'] ?></p>
                            </div>
                            <p>R <?= number_format($item['price'] * $item['quantity'], 2) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="border-t pt-4 mt-4 space-y-2">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>R <?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping</span>
                            <span>R <?= number_format($shipping, 2) ?></span>
                        </div>
                        <div class="flex justify-between font-bold text-lg pt-2">
                            <span>Total</span>
                            <span>R <?= number_format($total, 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Include Vue.js and the credit card form script -->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue-the-mask@0.11.1/dist/vue-the-mask.min.js"></script>

<script>
// Vue.js Credit Card Form
new Vue({
    el: "#app",
    data() {
        return {
            currentCardBackground: Math.floor(Math.random()* 25 + 1),
            cardName: "",
            cardNumber: "",
            cardMonth: "",
            cardYear: "",
            cardCvv: "",
            minCardYear: new Date().getFullYear(),
            amexCardMask: "#### ###### #####",
            otherCardMask: "#### #### #### ####",
            cardNumberTemp: "",
            isCardFlipped: false,
            focusElementStyle: null,
            isInputFocused: false
        };
    },
    mounted() {
        this.cardNumberTemp = this.otherCardMask;
    },
    computed: {
        getCardType () {
            let number = this.cardNumber;
            let re = new RegExp("^4");
            if (number.match(re) != null) return "visa";

            re = new RegExp("^(34|37)");
            if (number.match(re) != null) return "amex";

            re = new RegExp("^5[1-5]");
            if (number.match(re) != null) return "mastercard";

            re = new RegExp("^6011");
            if (number.match(re) != null) return "discover";
            
            re = new RegExp('^9792')
            if (number.match(re) != null) return 'troy'

            return "visa";
        },
        generateCardNumberMask () {
            return this.getCardType === "amex" ? this.amexCardMask : this.otherCardMask;
        },
        minCardMonth () {
            if (this.cardYear === this.minCardYear) return new Date().getMonth() + 1;
            return 1;
        }
    },
    watch: {
        cardYear () {
            if (this.cardMonth < this.minCardMonth) {
                this.cardMonth = "";
            }
        }
    },
    methods: {
        flipCard (status) {
            this.isCardFlipped = status;
        },
        focusInput (e) {
            this.isInputFocused = true;
            let targetRef = e.target.dataset.ref;
            let target = this.$refs[targetRef];
            this.focusElementStyle = {
                width: `${target.offsetWidth}px`,
                height: `${target.offsetHeight}px`,
                transform: `translateX(${target.offsetLeft}px) translateY(${target.offsetTop}px)`
            }
        },
        blurInput() {
            let vm = this;
            setTimeout(() => {
                if (!vm.isInputFocused) {
                    vm.focusElementStyle = null;
                }
            }, 300);
            vm.isInputFocused = false;
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>