
# PROJECT C - Audience & Campaigns (DaaS-style, privacy-first)

## Setup Instructions

### project Setup

run this:
   ```bash
   docker compose up --build
   ```

- The backend will be available at `http://localhost:8002`

  

## steps


### 1. Add New Audience
- `GET /api/subscribe` - add new audience
  
   ```text
    Idempotency-Key: audience:474545
      
   ```

   ```json
   {
    "email": "her@gmail.com",
    "segment": "new users"
  }
  ```
   

### 2. Load Audience
- `POST /api/audiences` -Load Audience (paginated)
  



### 3. Estimate Audience Size
- `POST /api/estimate-audience-size` - Estimate Audience Size

-   ### Example Request body

 ```json
    {
    "email":[
        "her@gmail.com",
        "herc@gmail.com"
    ],
    "segment": "new users b"
}
```

 

### 4. Create Campaign
- `POST /api/estimate-audience-size` - Create Campaign

- ### Example request header
  ```text
   Idempotency-Key:campaign:rewevfsfss
  ```


-   ### Example Request body

 ```json
  {
    "name": "welcome ccc",
    "content": "hi new users",
    "segment": "new users",
    "budget": 200
}
```



### 5. Activate Capaign
- `POST /api/webhooks/momo` - Activate Capaign

-  ### Example Request header

      ```text
     header{
        X-Signature: 1099920e82212e9c421a55c4278120244425ad6f3e3934a9c8d27af1b46df0b5,
      }
      
      ```


  ### Example Request Body

```json
{
        "name": "welcome ccc",
        "content": "hi new users",
        "segment": "new users",
        "budget": 200,
        "status": "draft",
        "idempotency_key": "campaign:rewevfs"
    }
```





### 6. Audience Last-touch
- `POST /api/audience/last-touch/{AUDIENCE_ID}` - Audience Last-touch












