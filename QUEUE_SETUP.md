# Queue Setup for Bulk Table Creation

The bulk table creation feature uses Laravel queues to process table creation in the background, preventing timeouts when creating multiple tables.

## Quick Start

### Development (using sync driver)
The application will work with the default `sync` queue driver, but jobs will run synchronously (blocking). For better performance, use one of the options below.

### Option 1: Database Queue (Recommended for small-scale)
```bash
# 1. Update .env
QUEUE_CONNECTION=database

# 2. Create jobs table (if not exists)
php artisan queue:table
php artisan migrate

# 3. Run the queue worker
php artisan queue:work
```

### Option 2: Redis Queue (Recommended for production)
```bash
# 1. Install Redis
brew install redis  # macOS
# OR
sudo apt-get install redis-server  # Ubuntu

# 2. Start Redis
redis-server

# 3. Update .env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# 4. Run the queue worker
php artisan queue:work redis
```

## Running the Queue Worker

### Foreground (for testing)
```bash
php artisan queue:work --verbose
```

### Background (for production)
Use a process manager like Supervisor:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/app/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/your/app/storage/logs/worker.log
```

## How It Works

1. When a vendor creates bulk tables, the request is immediately acknowledged
2. A `BulkCreateTablesJob` is dispatched to the queue
3. The job processes table creation in the background (up to 10 minutes)
4. Each table is created sequentially with QR code upload to Cloudinary
5. The frontend auto-refreshes after the estimated completion time
6. Logs are written to help track progress

## Monitoring

Check logs for job progress:
```bash
tail -f storage/logs/laravel.log | grep "bulk table"
```

## Troubleshooting

**Jobs not processing?**
- Ensure queue worker is running: `php artisan queue:work`
- Check the `failed_jobs` table: `SELECT * FROM failed_jobs;`
- Retry failed jobs: `php artisan queue:retry all`

**Timeout issues?**
- The job has a 10-minute timeout (`$timeout = 600`)
- Increase if needed in `app/Jobs/BulkCreateTablesJob.php`
- Or adjust PHP's `max_execution_time` setting

**Memory issues?**
- The queue worker may need more memory for large batches
- Add `--memory=512` to the queue:work command
