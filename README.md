
# PROJECT A - ORDER-PAY-NOTIFY

## Setup Instructions

### project Setup

3. run this:
   ```bash
   docker compose up --build
   ```

- The backend will be available at `http://localhost:8000`
- The frontend will be available at `http://localhost:5173`



## Project Structure

- `admin-ui/` - React frontend application
- `backend_api/` - Laravel backend API
  

## steps

### Backend API (Laravel)
- **Orders Management**: Create and view orders
- **Payment Processing**: Charge payments with idempotency
- **Product Management**: List products
- **Dashboard Metrics**: Real-time statistics
- **Webhook Support**: Handle payment webhooks



## API Endpoints
The backend is configured to allow requests from:
- `http://localhost:5173` (Vite dev server)
- `http://localhost:3000` (Alternative React dev server)


### products
- `GET /api/products` - List all products (paginated)

### orders
- `POST /api/orders` - Create new order
  
  ### Example Request Body

```json
{
  "items": [
    { "product_id": 5, "quantity": 535 },
    { "product_id": 4, "quantity": 515 }
  ]
}

```


### Payments
- `POST /api/payments/charge` - Charge a payment (requires Idempotency-Key)

-   ### Example Request header

  ```text
    Idempotency-Key: charge:3
      
   ```

  ### the number attached to charge:3 is the id of the order




### Webhook
- `POST /api/webhooks/momo` - Process payment

-    ### Example Request header

      ```text
     header{
        X-Signature: 7135a0d7ff10f393a00ca213b62719a0990722f892f89a09669e3ee14dd29e92,
      }
      
      ```


    ### Example Request Body

```json
{
        "order_id": 3,
        "amount": "142050.00",
        "status": "initiated",
        "idempotency_key": "charge:3",
        "id": 1
    }
```

### will get this playload after processing the payment 







