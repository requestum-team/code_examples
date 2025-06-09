# 🐛 Debug Configuration

## Prerequisites

**xDebug** is installed in php container

## Configuration

### Php Storm

1. Go to Preferences > PHP > Debug
   * Debug port: `9100` (or port configured in `./docker/php/conf.d/xdebug.ini`)
   * Enable “Can accept external connections”
2. Go to Preferences > PHP > Servers
   * Create new Server
     * give server name
     * host: `localhost`
     * port: `80`
     * click `Use path mappings` checkbox
     * in your project directory map `api` -> `/app`
3. Configure xDebug
   * Select server previously created
   * IDE key: `PHPSTORM`
