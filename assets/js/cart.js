document.addEventListener('DOMContentLoaded', () => {
    fetchCart();
});

function addToCart(id, qty = 1) {
    fetch('api/cart?action=add', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, qty }),
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            fetchCart(true); // true to open drawer
        }
    });
}

function updateCart(id, qty) {
    if (qty <= 0) return removeFromCart(id);
    fetch('api/cart?action=update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, qty }),
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) fetchCart();
    });
}

function removeFromCart(id) {
    fetch('api/cart?action=remove', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id }),
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) fetchCart();
    });
}

function fetchCart(openDrawer = false) {
    fetch('api/cart?action=fetch', { 
        cache: 'no-store',
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => {
        renderCart(data);
        if(openDrawer) {
            const drawer = document.getElementById('cart-drawer');
            if(drawer.classList.contains('translate-x-full')) {
                toggleCartDrawer();
            }
        }
    });
}

function renderCart(data) {
    // Update badge in navbar
    const navBadge = document.getElementById('nav-cart-count');
    if(navBadge) {
        navBadge.textContent = data.count;
        if (data.count > 0) {
            navBadge.classList.remove('opacity-0');
        } else {
            navBadge.classList.add('opacity-0');
        }
    }
    
    // Update header title in drawer
    const drawerTitle = document.querySelector('#cart-drawer h3');
    if(drawerTitle) {
        drawerTitle.innerHTML = `YOUR CART <span class="text-slate-500 text-sm ml-1 font-sans">(${data.count} Items)</span>`;
    }

    // Render body
    const body = document.getElementById('cart-drawer-body');
    if(!body) return;

    if (data.count === 0) {
        body.innerHTML = `
            <div class="flex-grow flex flex-col items-center justify-center p-8 text-center bg-[url('https://www.transparenttextures.com/patterns/cream-paper.png')] h-full">
                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mb-6 text-[#a3998f] shadow-sm border border-[#f4f2eb]">
                    <i class="fa-solid fa-bag-shopping text-4xl"></i>
                </div>
                <h4 class="font-serif text-2xl text-slate-800 mb-2">Your cart is empty</h4>
                <p class="text-slate-500 text-[14px] mb-8 max-w-[250px]">Looks like you haven't added any premium leather goods to your cart yet.</p>
                <button onclick="toggleCartDrawer()" class="bg-[#473121] text-white px-10 py-4 text-[12px] font-bold tracking-[0.15em] hover:bg-[#2c2521] transition-all duration-300 shadow-lg hover:shadow-xl w-full max-w-[280px]">
                    CONTINUE SHOPPING
                </button>
            </div>
        `;
        return;
    }

    let itemsHtml = '<div class="flex-grow p-6 flex flex-col gap-6 bg-[#fefdfc]">';
    
    data.items.forEach(item => {
        itemsHtml += `
            <div class="flex gap-4 border-b border-[#f4f2eb] pb-6 last:border-0 last:pb-0">
                <div class="w-[90px] h-[110px] bg-[#f8f8f8] shrink-0 border border-[#f0f0f0]">
                    <img src="${item.image}" alt="${item.name}" class="w-full h-full object-cover mix-blend-multiply">
                </div>
                <div class="flex flex-col flex-grow justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-1">
                            <h5 class="font-serif text-[#4d3c31] text-[15px] font-bold pr-2 leading-tight">${item.name}</h5>
                            <button onclick="removeFromCart(${item.id})" class="text-[#a3998f] hover:text-red-500 transition-colors">
                                <i class="fa-solid fa-trash-can text-[12px]"></i>
                            </button>
                        </div>
                        <p class="font-sans text-[11px] text-[#71685f]">${item.price_fmt}</p>
                    </div>
                    
                    <div class="flex justify-between items-center mt-3">
                        <div class="flex items-center border border-[#e2dcd0] bg-white">
                            <button onclick="updateCart(${item.id}, ${item.qty - 1})" class="w-8 h-8 flex items-center justify-center text-[#71685f] hover:text-[#4d3c31] hover:bg-[#f4f2eb] transition-colors">-</button>
                            <span class="w-8 text-center font-sans text-[12px] text-[#4d3c31] font-bold">${item.qty}</span>
                            <button onclick="updateCart(${item.id}, ${item.qty + 1})" class="w-8 h-8 flex items-center justify-center text-[#71685f] hover:text-[#4d3c31] hover:bg-[#f4f2eb] transition-colors">+</button>
                        </div>
                        <span class="font-sans text-[#4d3c31] font-bold text-[14px]">${item.subtotal_fmt}</span>
                    </div>
                </div>
            </div>
        `;
    });
    
    itemsHtml += '</div>';

    // Footer
    itemsHtml += `
        <div class="bg-white border-t border-[#f4f2eb] p-6 shadow-[0_-4px_20px_rgba(0,0,0,0.03)] mt-auto sticky bottom-0">
            <div class="flex justify-between items-center mb-6">
                <span class="font-sans text-[#71685f] text-[13px] uppercase tracking-widest font-semibold">Subtotal</span>
                <span class="font-sans text-[#4d3c31] text-[20px] font-bold">${data.total_fmt}</span>
            </div>
            <p class="text-[11px] text-[#a3998f] mb-4 text-center">Shipping, taxes, and discounts codes calculated at checkout.</p>
            <a href="checkout.php" class="block w-full bg-[#4a362a] text-white py-4 text-[12px] font-bold tracking-[0.2em] hover:bg-[#38281e] transition-colors shadow-lg hover:shadow-xl flex justify-center items-center gap-3">
                CHECKOUT <span class="font-sans font-normal text-[16px] leading-none ml-2">&rarr;</span>
            </a>
        </div>
    `;

    body.innerHTML = itemsHtml;
}


