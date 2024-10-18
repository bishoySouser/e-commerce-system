
# REST API Endpoints
- POST /api/resgister: Register user.

- POST /api/login: Login user.

- GET /api/products: Get all products.

- POST /api/orders: Create a new order.

- GET /api/orders/{id}: Get a specific order.





# testing:

```bash
  php artisan test
```

## functionality
### Units
- can transform range price filters

### features
- authenticated user can create order
- unauthenticated user cannot create order
- cannot create order with invalid quantity
- cannot create order with empty products 