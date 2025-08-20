// Wallet JavaScript for B-Cash
class WalletService {
    constructor() {
        this.apiUrl = '/api/wallet.php';
    }
    
    async getBalance() {
        try {
            const response = await fetch(`${this.apiUrl}?action=balance`);
            const result = await response.json();
            return result;
        } catch (error) {
            return { success: false, message: 'Failed to fetch balance' };
        }
    }
    
    async transferMoney(transferData) {
        try {
            const response = await fetch(`${this.apiUrl}?action=transfer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(transferData)
            });
            const result = await response.json();
            return result;
        } catch (error) {
            return { success: false, message: 'Transfer failed' };
        }
    }
    
    async searchAccount(account) {
        try {
            const response = await fetch(`${this.apiUrl}?action=search&account=${account}`);
            const result = await response.json();
            return result;
        } catch (error) {
            return { success: false, message: 'Search failed' };
        }
    }
}

// Initialize wallet service
const walletService = new WalletService();

// Form handlers
document.addEventListener('DOMContentLoaded', function() {
    // Balance display
    const balanceDisplay = document.getElementById('balanceDisplay');
    if (balanceDisplay) {
        walletService.getBalance().then(result => {
            if (result.success) {
                balanceDisplay.innerText = `Balance: ₱${result.data.balance}`;
            } else {
                alert(result.message);
            }
        });
    }
    
    // Transfer form
    const transferForm = document.getElementById('transferForm');
    if (transferForm) {
        transferForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const transferData = {
                receiver_account: formData.get('receiver_account'),
                amount: parseFloat(formData.get('amount')),
                description: formData.get('description')
            };
            
            const result = await walletService.transferMoney(transferData);
            
            if (result.success) {
                alert('Transfer successful! Reference: ' + result.data.reference_number);
                window.location.reload();
            } else {
                alert(result.message);
            }
        });
    }
    
    // Search account
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const account = formData.get('account');
            
            const result = await walletService.searchAccount(account);
            
            if (result.success) {
                alert(`Account found: ${result.data.full_name}`);
            } else {
                alert(result.message);
            }
        });
    }
});
