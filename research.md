# Research
This project has evolved from a testing assignment into a project research (maybe academic research). Currently, there are knowledge limitations in several key areas that require additional study. The development process involves writing extensive base-level code without framework support, which significantly impacts the project timeline and resource allocation.

## Monero
information: https://www.getmonero.org/2024/08/20/monero-0.18.3.4-released.html  
download: https://downloads.getmonero.org/cli/monero-linux-x64-v0.18.3.4.tar.bz2  
explorer: https://localmonero.co/blocks/  
rpc: https://docs.getmonero.org/rpc-library/monerod-rpc/

## Zcash
information: https://zcash.readthedocs.io/en/latest/rtd_pages/Debian-Ubuntu-build.html  
download: https://zcash.readthedocs.io/en/latest/rtd_pages/install_binary_tarball.html  
explorer: https://mainnet.zcashexplorer.app/  
rpc: https://zcash.github.io/rpc/

## Blockchain Forks
### Monero
- `hard_fork_info` version
- `get_version` have all hard fork version and their height
- check new version of github https://api.github.com/repos/monero-project/monero/releases/latest
- write notification system

### Zcash
- `getblockchaininfo` have activationheight for all forks and softforks have block version
- last block `major_version` and `minor_version `
- check transaction version
- check new version of github https://api.github.com/repos/zcash/zcash/releases/latest
- write notification system

## Market Capitalization
- Market Capitalization = Current Price x Circulating Supply
- In monero `total_emission` is equal to circulating supply.

## To-Do
- [x] Download and prepare blockchain nodes  
- [x] RPC connection
- [x] Create docker enviroment / Infrastructure
    - [x] Monero
    - [x] Zcash
    - [x] PostgreSQL
    - [x] PHP application
        - [x] API
        - [x] Script
- [ ] PHP Application
    - [x] Create .env loader
    - [x] Create logger interface
    - [x] Create database connection
    - [x] Create cli app for monero
        - [x] Interface for RPC connection
    - [x] Create cli app for zcash
        - [x] Interface for RPC connection
    - [x] Create rest api
    - [x] Block and transaction synchronization
    - [x] Database table design/architecture
    - [x] Database migration
    - [ ] Create soft and hard fork notification system
        - [ ] Check RPC height version
        - [ ] Check github release update
        - [ ] In database mark block that forked and planned height fork
        - [ ] Send email or telegram notification of incoming fork
    - [ ] Market Capitalization?
        - [ ] Write some postgresql procedure to aggregate data?
        - [ ] Research shielded/protected block/transaction to find way approximately analyze capitalization
- [ ] Documentation