import {Controller} from 'stimulus';

export default class extends Controller {
    static targets = ['image', 'json'];
    static values = {};

    image = (e) => {
        e.preventDefault();
        e.stopPropagation();

        const popoverElement = document.createElement('div');
        popoverElement.innerHTML = `<div class="atd-cfi-popover-overlay">
    <div class="atd-cfi-popover-contents">
        <div class="atd-cfi-popover-img">
            <img src="${e.currentTarget.href}" alt="">
        </div>
    </div>
</div>`;

        document.body.style.overflow = 'hidden';
        document.body.append(popoverElement);
        popoverElement.addEventListener('click', this.destroy);
    }

    json = async (e) => {
        e.preventDefault();
        e.stopPropagation();

        const post = this.getPost(e.currentTarget.href);

        const popoverElement = document.createElement('div');
        popoverElement.innerHTML = `<div class="atd-cfi-popover-overlay">
    <div class="atd-cfi-popover-contents">
        <div style="padding: 40px;">
            <div style="padding: 18px;">
                <div class="spinner-loader"></div>
            </div>
        </div>
    </div>
</div>`;

        document.body.style.overflow = 'hidden';
        document.body.append(popoverElement);
        popoverElement.addEventListener('click', this.destroy);

        post.then(post => {
            const parent = popoverElement.querySelector('.spinner-loader').parentElement;
            parent.style.backgroundColor = '#fff';
            parent.innerHTML = `<h3>${post[0].title.rendered}</h3>${post[0].content.rendered}`;
        });
    }

    destroy = (e) => {
        document.body.style.overflow = '';
        e.currentTarget.remove();
    }

    async getPost(url) {
        const response = await fetch(url);
        return await response.json();
    }
}