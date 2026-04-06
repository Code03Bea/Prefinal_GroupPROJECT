<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Order Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('background1.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .form-container {
            padding: 40px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .full-width {
            grid-column: 1 / -1;
        }
        .products-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 2px dashed #dee2e6;
        }
        .radio-group, .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .radio-item, .checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .radio-item input, .checkbox-item input {
            width: auto;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
            width: 100%;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
        }
        .error {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }
        @media (max-width: 768px) {
            .row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Online Order Form</h1>
            <p>Place your order with us today!</p>
        </div>
        
        <div class="form-container">
            <?php
 
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
                $first_name = trim(htmlspecialchars(stripslashes($_POST['first_name'] ?? '')));
                $last_name = trim(htmlspecialchars(stripslashes($_POST['last_name'] ?? '')));
                $address = trim(htmlspecialchars(stripslashes($_POST['address'] ?? '')));
                $contact = trim(htmlspecialchars(stripslashes($_POST['contact'] ?? '')));
                $email = trim(htmlspecialchars(stripslashes($_POST['email'] ?? '')));
                $products = $_POST['products'] ?? [];
                $quantity = intval($_POST['quantity'] ?? 0);
                $payment = trim(htmlspecialchars(stripslashes($_POST['payment'] ?? '')));
                $delivery = trim(htmlspecialchars(stripslashes($_POST['delivery'] ?? '')));
                $notes = trim(htmlspecialchars(stripslashes($_POST['notes'] ?? '')));

                $errors = [];
                
                if (empty($first_name)) $errors[] = "First Name is required";
                if (empty($last_name)) $errors[] = "Last Name is required";
                if (empty($address)) $errors[] = "Address is required";
                if (empty($contact) || !preg_match('/^[0-9+\-\s()]{10,15}$/', $contact)) $errors[] = "Valid Contact Number is required";
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid Email is required";
                if (empty($products)) $errors[] = "Please select at least one product";
                if ($quantity < 1 || $quantity > 100) $errors[] = "Quantity must be between 1-100";
                if (empty($payment)) $errors[] = "Please select payment method";
                if (empty($delivery)) $errors[] = "Please select delivery option";

                if (empty($errors)) {
   
                    $product_prices = [
                        'pizza' => 12.99, 'burger' => 8.99, 'pasta' => 10.99, 'salad' => 7.99,
                        'sushi' => 14.99, 'taco' => 6.99, 'sandwich' => 5.99, 'wrap' => 7.49,
                        'noodles' => 9.99, 'dessert' => 4.99
                    ];
                    
                    $total = 0;
                    $selected_products = [];
                    
                    foreach ($products as $product) {
                        if (isset($product_prices[$product])) {
                            $price = $product_prices[$product];
                            $item_total = $price * $quantity;
                            $total += $item_total;
                            $selected_products[] = "$product ($$price x $quantity = $$item_total)";
                        }
                    }

                    $query_string = http_build_query([
                        'success' => 1,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'address' => $address,
                        'contact' => $contact,
                        'email' => $email,
                        'products' => implode(', ', $selected_products),
                        'quantity' => $quantity,
                        'payment' => $payment,
                        'delivery' => $delivery,
                        'total' => number_format($total, 2),
                        'notes' => $notes
                    ]);
                    header("Location: index.php?" . $query_string);
                    exit;
                }
            }

            if (isset($_GET['success']) && $_GET['success'] == 1) {
                echo '<div style="background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #c3e6cb;">';
                echo '<h3 style="margin: 0 0 10px 0;">✅ Order Placed Successfully!</h3>';
                echo '<p><strong>Customer:</strong> ' . htmlspecialchars($_GET['first_name'] ?? '') . ' ' . htmlspecialchars($_GET['last_name'] ?? '') . '</p>';
                echo '<p><strong>Products:</strong> ' . htmlspecialchars($_GET['products'] ?? '') . '</p>';
                echo '<p><strong>Total Amount:</strong> $' . htmlspecialchars($_GET['total'] ?? '0.00') . '</p>';
                echo '<p><strong>Payment:</strong> ' . htmlspecialchars($_GET['payment'] ?? '') . '</p>';
                echo '<p><strong>Delivery:</strong> ' . htmlspecialchars($_GET['delivery'] ?? '') . '</p>';
                echo '</div>';
            }

            if (isset($errors) && !empty($errors)) {
                echo '<div style="background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #f5c6cb;">';
                echo '<h3 style="margin: 0 0 10px 0;">⚠️ Please fix the following errors:</h3>';
                echo '<ul style="margin: 0;">';
                foreach ($errors as $error) {
                    echo '<li>' . htmlspecialchars($error) . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }
            ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">

                <h2 style="margin-bottom: 30px; color: #333;">👤 Customer Information</h2>
                
                <div class="row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="address">Address *</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>" required>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="contact">Contact Number *</label>
                        <input type="tel" id="contact" name="contact" value="<?php echo htmlspecialchars($_POST['contact'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>
                </div>

   
                <h2 style="margin: 40px 0 30px 0; color: #333;">🍽️ Order Details</h2>
                
                <div class="form-group full-width products-section">
                    <label>Product Selection * (Select one or more)</label>
                    <div class="checkbox-group" style="margin-top: 10px;">
                        <?php
                        $products = [
                            'pizza' => '🍕 Pizza - ₱219',
                            'burger' => '🍔 Burger - ₱50',
                            'pasta' => '🍝 Pasta - ₱85',
                            'salad' => '🥗 Salad - ₱130',
                            'sushi' => '🍣 Sushi - ₱300',
                            'taco' => '🌮 Taco - ₱290',
                            'sandwich' => '🥪 Sandwich - ₱40',
                            'wrap' => '🌯 Wrap - ₱60',
                            'noodles' => '🍜 Noodles - ₱55',
                            'dessert' => '🍰 Dessert - ₱200'
                        ];
                        
                        foreach ($products as $key => $label) {
                            $checked = isset($_POST['products']) && in_array($key, $_POST['products']) ? 'checked' : '';
                            echo "<label class='checkbox-item'>
                                    <input type='checkbox' name='products[]' value='$key' $checked> $label
                                  </label>";
                        }
                        ?>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="quantity">Quantity *</label>
                        <input type="number" id="quantity" name="quantity" min="1" max="100" value="<?php echo htmlspecialchars($_POST['quantity'] ?? '1'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Mode of Payment *</label>
                        <div class="radio-group">
                            <label class="radio-item">
                                <input type="radio" name="payment" value="Credit Card" <?php echo (($_POST['payment'] ?? '') == 'Credit Card') ? 'checked' : ''; ?>> 💳 Credit Card
                            </label>
                            <label class="radio-item">
                                <input type="radio" name="payment" value="Debit Card" <?php echo (($_POST['payment'] ?? '') == 'Debit Card') ? 'checked' : ''; ?>> 🪪 Debit Card
                            </label>
                            <label class="radio-item">
                                <input type="radio" name="payment" value="PayPal" <?php echo (($_POST['payment'] ?? '') == 'PayPal') ? 'checked' : ''; ?>> 💰 PayPal
                            </label>
                            <label class="radio-item">
                                <input type="radio" name="payment" value="Cash on Delivery" <?php echo (($_POST['payment'] ?? '') == 'Cash on Delivery') ? 'checked' : ''; ?>> 💵 Cash on Delivery
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="delivery">Delivery Option *</label>
                        <select id="delivery" name="delivery" required>
                            <option value="">Select delivery option</option>
                            <option value="Standard (3-5 days)" <?php echo (($_POST['delivery'] ?? '') == 'Standard (3-5 days)') ? 'selected' : ''; ?>>🚚 Standard (3-5 days) - Free</option>
                            <option value="Express (1-2 days)" <?php echo (($_POST['delivery'] ?? '') == 'Express (1-2 days)') ? 'selected' : ''; ?>>✈️ Express (1-2 days) - $5.99</option>
                            <option value="Same Day" <?php echo (($_POST['delivery'] ?? '') == 'Same Day') ? 'selected' : ''; ?>>⚡ Same Day - $12.99</option>
                        </select>
                    </div>
                    <div class="form-group"></div>
                </div>

                <div class="form-group full-width">
                    <label for="notes">Additional Notes (Optional)</label>
                    <textarea id="notes" name="notes" placeholder="Any special instructions?"><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="submit-btn">🚀 Place Order</button>
            </form>
        </div>
    </div>
</body>
</html>