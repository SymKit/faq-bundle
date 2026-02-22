import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['item', 'button', 'content'];

    toggle(event) {
        const button = event.currentTarget;
        const targetId = button.getAttribute('aria-controls');
        const content = this.contentTargets.find(el => el.id === targetId);

        if (!content) return;

        const isExpanded = button.getAttribute('aria-expanded') === 'true';

        // If we want only one open at a time
        if (!isExpanded) {
            this.closeAll();
        }

        this.setExpanded(button, content, !isExpanded);
    }

    closeAll() {
        this.buttonTargets.forEach((button, index) => {
            const content = this.contentTargets[index];
            this.setExpanded(button, content, false);
        });
    }

    setExpanded(button, content, expanded) {
        button.setAttribute('aria-expanded', expanded.toString());

        if (expanded) {
            content.classList.remove('hidden');
        } else {
            content.classList.add('hidden');
        }
    }
}
