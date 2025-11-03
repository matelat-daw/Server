$css = @"
/* Cart Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.modal-content.cart-modal {
    background: white;
    border-radius: 12px;
    max-width: 800px;
    width: 90%;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
}

.modal-header {
    padding: 20px 30px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.5em;
}

.close-btn {
    background: none;
    border: none;
    font-size: 2em;
    cursor: pointer;
    color: #666;
    line-height: 1;
    padding: 0;
    width: 40px;
    height: 40px;
}

.close-btn:hover {
    color: #333;
}

.modal-body {
    padding: 20px 30px;
    overflow-y: auto;
    flex: 1;
}

.cart-item {
    display: grid;
    grid-template-columns: 80px 1fr auto auto auto;
    gap: 15px;
    align-items: center;
    padding: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    margin-bottom: 15px;
    background: #f9f9f9;
}

.cart-item-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}

.cart-item-details {
    flex: 1;
}

.cart-item-details h4 {
    margin: 0 0 5px 0;
    font-size: 1.1em;
}

.cart-item-category {
    color: #666;
    font-size: 0.9em;
    margin: 3px 0;
}

.cart-item-price {
    font-weight: bold;
    color: #2196F3;
    margin: 3px 0;
}

.cart-item-quantity {
    display: flex;
    align-items: center;
    gap: 5px;
}

.qty-btn {
    background: #2196F3;
    color: white;
    border: none;
    width: 30px;
    height: 30px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1.2em;
}

.qty-btn:hover {
    background: #1976D2;
}

.qty-input {
    width: 50px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 5px;
    font-size: 1em;
}

.cart-item-subtotal {
    font-weight: bold;
    font-size: 1.1em;
    min-width: 80px;
    text-align: right;
}

.remove-btn {
    background: #f44336;
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1.2em;
}

.remove-btn:hover {
    background: #d32f2f;
}

.modal-footer {
    padding: 20px 30px;
    border-top: 1px solid #e0e0e0;
}

.cart-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1.3em;
    margin-bottom: 20px;
    padding: 15px;
    background: #f5f5f5;
    border-radius: 8px;
}

#cart-total-amount {
    color: #4CAF50;
    font-weight: bold;
    font-size: 1.2em;
}

.cart-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn-primary, .btn-secondary {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1em;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-primary {
    background: #4CAF50;
    color: white;
}

.btn-primary:hover {
    background: #45a049;
}

.btn-secondary {
    background: #666;
    color: white;
}

.btn-secondary:hover {
    background: #555;
}

@media (max-width: 768px) {
    .cart-item {
        grid-template-columns: 60px 1fr;
        gap: 10px;
    }
    
    .cart-item-quantity,
    .cart-item-subtotal {
        grid-column: 2;
    }
    
    .remove-btn {
        grid-column: 2;
        justify-self: end;
    }
}
"@

[System.IO.File]::WriteAllText("C:\Server\html\Nueva-WEB\frontend\components\cart\cart.css", $css)
