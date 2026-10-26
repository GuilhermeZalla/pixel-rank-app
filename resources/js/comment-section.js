const form = document.getElementById('comment-form');
const panel = document.getElementById('comments-panel');
const comments = document.getElementById('comments');
const count = document.getElementById('comments-count');
const emptyState = document.getElementById('comments-empty');
const feedback = document.getElementById('comment-feedback');
const listFeedback = document.getElementById('comments-feedback');
const sortSelect = document.getElementById('comments-sort');
const loadMoreButton = document.getElementById('comments-load-more');

function showFeedback(message, isError = false) {
    if (!feedback) return;

    feedback.textContent = message;
    feedback.className = `mt-3 rounded-lg px-3 py-2 text-sm ${isError ? 'bg-error/10 text-error' : 'bg-primary/10 text-primary'}`;
}

function showListError(message) {
    if (!listFeedback) return;
    listFeedback.textContent = message;
    listFeedback.classList.remove('hidden');
}

function updateCommentCount(total) {
    count.textContent = String(total);
    const compactCount = document.getElementById('review-comments-count');
    if (!compactCount) return;
    const template = total === 0 ? compactCount.dataset.zero : total === 1 ? compactCount.dataset.one : compactCount.dataset.many;
    compactCount.textContent = template.replace(':count', String(total));
}

function makeElement(tag, className, text = null) {
    const element = document.createElement(tag);
    if (className) element.className = className;
    if (text !== null) element.textContent = text;
    return element;
}

function renderEditForm(comment) {
    const editForm = document.createElement('form');
    editForm.className = 'comment-edit-form mt-3 hidden';
    editForm.dataset.id = String(comment.id);

    const label = makeElement('label', 'sr-only', panel.dataset.editFieldLabel);
    label.htmlFor = `comment-edit-${comment.id}`;

    const textarea = makeElement('textarea', 'comment-edit-input w-full resize-y rounded-xl border border-base-content/15 bg-base-200 p-3 text-sm text-base-content focus:border-accent focus:outline-none focus-visible:ring-2 focus-visible:ring-accent');
    textarea.id = `comment-edit-${comment.id}`;
    textarea.rows = 4;
    textarea.minLength = 5;
    textarea.maxLength = 2000;
    textarea.required = true;
    textarea.value = comment.body;
    textarea.setAttribute('aria-describedby', `comment-edit-error-${comment.id}`);

    const error = makeElement('p', 'comment-edit-error mt-2 hidden text-sm text-error');
    error.id = `comment-edit-error-${comment.id}`;
    error.setAttribute('role', 'alert');

    const buttons = makeElement('div', 'mt-3 flex flex-wrap gap-2');
    const save = makeElement('button', 'comment-edit-save btn btn-primary min-h-11', panel.dataset.editSave);
    save.type = 'submit';
    const cancel = makeElement('button', 'comment-edit-cancel btn btn-ghost min-h-11', panel.dataset.editCancel);
    cancel.type = 'button';
    buttons.append(save, cancel);
    editForm.append(label, textarea, error, buttons);

    return editForm;
}

function renderReplyForm(comment) {
    const replyForm = makeElement('form', 'reply-comment-form mt-3 hidden');
    replyForm.dataset.id = String(comment.id);

    const label = makeElement('label', 'sr-only', panel.dataset.replyLabel);
    label.htmlFor = `comment-reply-${comment.id}`;

    const textarea = makeElement('textarea', 'reply-comment-input w-full resize-y rounded-xl border border-base-content/15 bg-base-200 p-3 text-sm text-base-content focus:border-accent focus:outline-none focus-visible:ring-2 focus-visible:ring-accent');
    textarea.id = `comment-reply-${comment.id}`;
    textarea.rows = 3;
    textarea.minLength = 5;
    textarea.maxLength = 2000;
    textarea.required = true;
    textarea.placeholder = panel.dataset.replyPlaceholder;

    const error = makeElement('p', 'reply-comment-error mt-2 hidden text-sm text-error');
    error.setAttribute('role', 'alert');

    const buttons = makeElement('div', 'mt-3 flex flex-wrap gap-2');
    const submit = makeElement('button', 'reply-comment-submit btn btn-primary min-h-11', panel.dataset.replySubmit);
    submit.type = 'submit';
    const cancel = makeElement('button', 'reply-comment-cancel btn btn-ghost min-h-11', panel.dataset.editCancel);
    cancel.type = 'button';
    buttons.append(submit, cancel);
    replyForm.append(label, textarea, error, buttons);

    return replyForm;
}

function renderComment(comment) {
    const item = makeElement('li', 'comment-card group flex min-w-0 gap-3 rounded-2xl border border-base-content/10 bg-base-300 p-4 transition-colors hover:border-primary/40 sm:gap-4 sm:p-5');
    item.id = `comment-${comment.id}`;

    const avatar = makeElement('img', 'size-11 shrink-0 rounded-full object-cover ring-1 ring-base-content/10 sm:size-12');
    avatar.src = comment.user.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user.nickname)}`;
    avatar.alt = '';
    avatar.setAttribute('aria-hidden', 'true');
    item.append(avatar);

    const bodyColumn = makeElement('div', 'min-w-0 flex-1');
    const header = makeElement('div', 'flex flex-wrap items-start justify-between gap-x-3 gap-y-2');
    const metadata = makeElement('div', 'flex min-w-0 flex-wrap items-center gap-x-2 gap-y-1');
    metadata.append(makeElement('h3', 'break-words text-sm font-bold text-base-content', comment.user.nickname));

    if (Number(comment.user_id) === Number(panel.dataset.reviewAuthorId)) {
        const badge = makeElement('span', 'rounded-full bg-primary/15 px-2 py-0.5 text-[.68rem] font-bold text-primary', panel.dataset.authorLabel);
        badge.setAttribute('aria-label', panel.dataset.authorLabel);
        metadata.append(badge);
    }

    const time = makeElement('time', 'text-xs text-base-content/60', comment.posted_date);
    time.dateTime = comment.created_at || '';
    metadata.append(time);
    header.append(metadata);

    const isOwner = panel.dataset.canManage === 'true'
        && Number(comment.user_id) === Number(panel.dataset.currentUserId);
    if (isOwner) {
        const actions = makeElement('div', 'flex flex-wrap gap-1');
        const edit = makeElement('button', 'edit-comment min-h-11 rounded-lg px-3 text-sm font-semibold text-primary transition-colors hover:bg-primary/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent', panel.dataset.editLabel);
        edit.type = 'button';
        edit.dataset.id = String(comment.id);
        const remove = makeElement('button', 'delete-comment min-h-11 min-w-11 rounded-lg px-3 text-sm font-semibold text-error transition-colors hover:bg-error/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent', panel.dataset.deleteLabel);
        remove.type = 'button';
        remove.dataset.id = String(comment.id);
        remove.setAttribute('aria-label', `${panel.dataset.deleteLabel}: ${comment.user.nickname}`);
        actions.append(edit, remove);
        header.append(actions);
    }

    bodyColumn.append(header);
    const content = makeElement('div', 'comment-content');
    if (comment.reply_to_id && comment.reply_to?.user) {
        content.append(makeElement('p', 'mt-3 text-xs text-base-content/60', panel.dataset.replyingToLabel.replace(':nickname', comment.reply_to.user.nickname)));
    }
    content.append(makeElement('p', 'comment-body mt-3 whitespace-pre-wrap break-words [overflow-wrap:anywhere] text-sm leading-relaxed text-base-content/90', comment.body));
    bodyColumn.append(content);
    if (isOwner) bodyColumn.append(renderEditForm(comment));

    if (panel.dataset.canManage === 'true') {
        const interactions = makeElement('div', 'comment-interactions mt-3 flex flex-wrap items-center gap-2');
        const reply = makeElement('button', 'reply-comment min-h-11 rounded-lg px-3 text-sm font-semibold text-primary transition-colors hover:bg-primary/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent', panel.dataset.replyLabel);
        reply.type = 'button';
        reply.dataset.id = String(comment.id);

        const isLiked = comment.liked_by_current_user === true || comment.liked_by_current_user === 1;
        const like = makeElement('button', `comment-like min-h-11 rounded-lg px-3 text-sm font-semibold transition-colors hover:bg-primary/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent ${isLiked ? 'text-primary' : 'text-base-content/70'}`);
        like.type = 'button';
        like.dataset.id = String(comment.id);
        like.setAttribute('aria-pressed', String(isLiked));
        like.append(makeElement('span', 'comment-like-label', isLiked ? panel.dataset.likedLabel : panel.dataset.likeLabel));
        like.append(document.createTextNode(' '));
        like.append(makeElement('span', 'comment-like-count', String(comment.likes_count || 0)));
        interactions.append(reply, like);
        bodyColumn.append(interactions, renderReplyForm(comment));
    }
    item.append(bodyColumn);

    return item;
}

async function updateCommentLike(button) {
    const wasLiked = button.getAttribute('aria-pressed') === 'true';
    if (button.disabled) return;

    button.disabled = true;
    button.setAttribute('aria-busy', 'true');
    try {
        const response = await fetch(`/comments/${button.dataset.id}/like`, {
            method: wasLiked ? 'DELETE' : 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                Accept: 'application/json',
            },
        });
        const payload = response.headers.get('content-type')?.includes('application/json')
            ? await response.json()
            : null;
        if (!response.ok || !payload || typeof payload.likes_count !== 'number') throw new Error();

        button.setAttribute('aria-pressed', String(payload.liked_by_current_user));
        button.querySelector('.comment-like-label').textContent = payload.liked_by_current_user
            ? panel.dataset.likedLabel
            : panel.dataset.likeLabel;
        button.querySelector('.comment-like-count').textContent = String(payload.likes_count);
        button.classList.toggle('text-primary', payload.liked_by_current_user);
        button.classList.toggle('text-base-content/70', !payload.liked_by_current_user);
    } catch {
        showFeedback(panel.dataset.likeError, true);
    } finally {
        button.disabled = false;
        button.removeAttribute('aria-busy');
    }
}

async function fetchComments(offset, sort = sortSelect?.value || 'newest') {
    const url = new URL(panel.dataset.commentsUrl, window.location.origin);
    url.searchParams.set('sort', sort);
    url.searchParams.set('offset', String(offset));
    const response = await fetch(url, { headers: { Accept: 'application/json' } });
    const payload = response.headers.get('content-type')?.includes('application/json')
        ? await response.json()
        : null;

    if (!response.ok || !payload?.comments) throw new Error(panel.dataset.sortError);
    return payload;
}

function updateListState(payload) {
    updateCommentCount(payload.total);
    emptyState?.classList.toggle('hidden', payload.total > 0);
    loadMoreButton.dataset.offset = String(payload.next_offset);
    loadMoreButton.dataset.hasMore = String(payload.has_more);
    loadMoreButton.classList.toggle('hidden', !payload.has_more);
}

async function refreshComments(targetCount = comments.children.length) {
    let offset = 0;
    let latestPayload = { total: Number(count.textContent), next_offset: 0, has_more: false };
    const refreshedComments = [];

    while (offset < targetCount) {
        const payload = await fetchComments(offset);
        refreshedComments.push(...payload.comments);
        latestPayload = payload;
        offset = payload.next_offset;
        if (!payload.has_more || payload.comments.length === 0 || offset >= targetCount) break;
    }

    comments.replaceChildren(...refreshedComments.map(renderComment));
    latestPayload.next_offset = comments.children.length;
    latestPayload.has_more = comments.children.length < latestPayload.total;
    updateListState(latestPayload);
}

if (sortSelect && comments && panel) {
    sortSelect.addEventListener('change', async () => {
        sortSelect.disabled = true;
        loadMoreButton.disabled = true;
        listFeedback.classList.add('hidden');
        try {
            const payload = await fetchComments(0, sortSelect.value);
            comments.replaceChildren(...payload.comments.map(renderComment));
            updateListState(payload);
        } catch {
            showListError(panel.dataset.sortError);
        } finally {
            sortSelect.disabled = false;
            loadMoreButton.disabled = false;
        }
    });

    loadMoreButton?.addEventListener('click', async () => {
        if (loadMoreButton.disabled || loadMoreButton.dataset.hasMore !== 'true') return;
        loadMoreButton.disabled = true;
        loadMoreButton.setAttribute('aria-busy', 'true');
        loadMoreButton.textContent = loadMoreButton.dataset.loading;
        listFeedback.classList.add('hidden');
        try {
            const payload = await fetchComments(Number(loadMoreButton.dataset.offset));
            payload.comments.forEach((comment) => comments.append(renderComment(comment)));
            updateListState(payload);
        } catch {
            showListError(panel.dataset.sortError);
        } finally {
            loadMoreButton.disabled = false;
            loadMoreButton.removeAttribute('aria-busy');
            loadMoreButton.textContent = loadMoreButton.dataset.label;
        }
    });
}

if (form && comments && count) {
    const textarea = form.querySelector('#comment');
    const characterCount = document.getElementById('comment-character-count');
    const fieldError = document.getElementById('comment-error');
    const submitButton = document.getElementById('comment-submit');
    const submitLabel = document.getElementById('comment-submit-label');
    const submitSpinner = document.getElementById('comment-submit-spinner');

    function updateCharacterCount() {
        characterCount.textContent = String(textarea.value.length);
    }

    function clearFieldError() {
        fieldError.textContent = '';
        fieldError.classList.add('hidden');
        textarea.removeAttribute('aria-invalid');
    }

    function showFieldError(message) {
        fieldError.textContent = message;
        fieldError.classList.remove('hidden');
        textarea.setAttribute('aria-invalid', 'true');
        showFeedback(message, true);
        textarea.focus();
    }

    updateCharacterCount();
    textarea.addEventListener('input', () => {
        updateCharacterCount();
        if (textarea.value.length >= Number(textarea.minLength) && fieldError.textContent) {
            clearFieldError();
            feedback.classList.add('hidden');
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (submitButton.disabled) return;
        clearFieldError();

        if (textarea.value.length < Number(textarea.minLength)) return showFieldError(form.dataset.errorShort);
        if (textarea.value.length > Number(textarea.maxLength)) return showFieldError(form.dataset.errorLong);

        submitButton.disabled = true;
        submitButton.setAttribute('aria-busy', 'true');
        submitSpinner.classList.remove('hidden');
        submitLabel.textContent = form.dataset.sending;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value,
                    Accept: 'application/json',
                },
                body: new FormData(form),
            });
            const payload = response.headers.get('content-type')?.includes('application/json')
                ? await response.json()
                : null;

            if (response.status === 422) return showFieldError(payload?.errors?.comment?.[0] || form.dataset.errorGeneric);
            if (!response.ok || !payload?.comment?.user) return showFieldError(form.dataset.errorGeneric);

            const newComment = renderComment(payload.comment);
            const loadedCount = comments.children.length;
            const totalAfterPost = Number(count.textContent) + 1;
            updateCommentCount(totalAfterPost);
            emptyState?.classList.add('hidden');

            if (sortSelect.value === 'newest') {
                comments.prepend(newComment);
                if (loadMoreButton.dataset.hasMore === 'true' && comments.children.length > loadedCount) {
                    comments.lastElementChild.remove();
                }
            } else if (loadMoreButton.dataset.hasMore !== 'true') {
                comments.append(newComment);
            }

            loadMoreButton.dataset.offset = String(comments.children.length);
            loadMoreButton.dataset.hasMore = String(comments.children.length < totalAfterPost);
            loadMoreButton.classList.toggle('hidden', loadMoreButton.dataset.hasMore !== 'true');
            form.reset();
            clearFieldError();
            updateCharacterCount();
            showFeedback(form.dataset.success);
        } catch {
            showFieldError(form.dataset.errorGeneric);
        } finally {
            submitButton.disabled = false;
            submitButton.removeAttribute('aria-busy');
            submitSpinner.classList.add('hidden');
            submitLabel.textContent = form.dataset.submitLabel;
        }
    });

    textarea.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && event.ctrlKey) {
            event.preventDefault();
            form.requestSubmit();
        }
    });
}

if (comments && panel) {
    comments.addEventListener('click', (event) => {
        const replyButton = event.target.closest?.('.reply-comment');
        if (replyButton) {
            const item = replyButton.closest('.comment-card');
            const replyForm = item.querySelector('.reply-comment-form');
            replyForm.classList.remove('hidden');
            replyForm.querySelector('.reply-comment-input').focus();
            return;
        }

        const likeButton = event.target.closest?.('.comment-like');
        if (likeButton) {
            void updateCommentLike(likeButton);
            return;
        }

        const editButton = event.target.closest?.('.edit-comment');
        if (editButton) {
            const item = editButton.closest('.comment-card');
            item.querySelector('.comment-content').classList.add('hidden');
            item.querySelector('.comment-edit-form').classList.remove('hidden');
            item.querySelector('.comment-edit-input').focus();
            return;
        }

        const cancelButton = event.target.closest?.('.comment-edit-cancel');
        if (cancelButton) {
            const editForm = cancelButton.closest('.comment-edit-form');
            const item = cancelButton.closest('.comment-card');
            editForm.querySelector('.comment-edit-input').value = item.querySelector('.comment-body').textContent;
            editForm.querySelector('.comment-edit-error').classList.add('hidden');
            editForm.classList.add('hidden');
            item.querySelector('.comment-content').classList.remove('hidden');
            item.querySelector('.edit-comment').focus();
            return;
        }

        const replyCancelButton = event.target.closest?.('.reply-comment-cancel');
        if (replyCancelButton) {
            const replyForm = replyCancelButton.closest('.reply-comment-form');
            const item = replyCancelButton.closest('.comment-card');
            replyForm.querySelector('.reply-comment-input').value = '';
            replyForm.querySelector('.reply-comment-error').classList.add('hidden');
            replyForm.classList.add('hidden');
            item.querySelector('.reply-comment').focus();
        }
    });

    comments.addEventListener('submit', async (event) => {
        const replyForm = event.target.closest?.('.reply-comment-form');
        if (replyForm) {
            event.preventDefault();
            const input = replyForm.querySelector('.reply-comment-input');
            const error = replyForm.querySelector('.reply-comment-error');
            const submitButton = replyForm.querySelector('.reply-comment-submit');
            const cancelButton = replyForm.querySelector('.reply-comment-cancel');
            if (submitButton.disabled) return;
            error.textContent = '';
            error.classList.add('hidden');

            if (input.value.length < input.minLength || input.value.length > input.maxLength) {
                error.textContent = input.value.length < input.minLength ? panel.dataset.errorShort : panel.dataset.errorLong;
                error.classList.remove('hidden');
                input.focus();
                return;
            }

            submitButton.disabled = true;
            cancelButton.disabled = true;
            submitButton.setAttribute('aria-busy', 'true');
            submitButton.textContent = panel.dataset.replySending;
            try {
                const response = await fetch('/comments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({
                        comment: input.value,
                        review: panel.dataset.reviewId,
                        reply_to: replyForm.dataset.id,
                    }),
                });
                const payload = response.headers.get('content-type')?.includes('application/json')
                    ? await response.json()
                    : null;

                if (response.status === 422) {
                    error.textContent = payload?.errors?.comment?.[0] || panel.dataset.replyError;
                    error.classList.remove('hidden');
                    return;
                }
                if (!response.ok || !payload?.comment?.user) throw new Error();

                const loadedCount = comments.children.length;
                const totalAfterReply = Number(count.textContent) + 1;
                updateCommentCount(totalAfterReply);
                emptyState?.classList.add('hidden');
                if (sortSelect?.value === 'newest') {
                    comments.prepend(renderComment(payload.comment));
                    if (loadMoreButton.dataset.hasMore === 'true' && comments.children.length > loadedCount) {
                        comments.lastElementChild.remove();
                    }
                } else if (loadMoreButton.dataset.hasMore !== 'true') {
                    comments.append(renderComment(payload.comment));
                }
                loadMoreButton.dataset.offset = String(comments.children.length);
                loadMoreButton.dataset.hasMore = String(comments.children.length < totalAfterReply);
                loadMoreButton.classList.toggle('hidden', loadMoreButton.dataset.hasMore !== 'true');
                input.value = '';
                replyForm.classList.add('hidden');
                showFeedback(panel.dataset.commentPosted);
            } catch {
                error.textContent = panel.dataset.replyError;
                error.classList.remove('hidden');
            } finally {
                submitButton.disabled = false;
                cancelButton.disabled = false;
                submitButton.removeAttribute('aria-busy');
                submitButton.textContent = panel.dataset.replySubmit;
            }
            return;
        }

        const editForm = event.target.closest?.('.comment-edit-form');
        if (!editForm) return;
        event.preventDefault();
        const input = editForm.querySelector('.comment-edit-input');
        const error = editForm.querySelector('.comment-edit-error');
        const saveButton = editForm.querySelector('.comment-edit-save');
        const cancelButton = editForm.querySelector('.comment-edit-cancel');
        if (saveButton.disabled) return;
        error.textContent = '';
        error.classList.add('hidden');

        if (input.value.length < input.minLength || input.value.length > input.maxLength) {
            error.textContent = input.value.length < input.minLength ? form.dataset.errorShort : form.dataset.errorLong;
            error.classList.remove('hidden');
            input.focus();
            return;
        }

        saveButton.disabled = true;
        cancelButton.disabled = true;
        saveButton.setAttribute('aria-busy', 'true');
        saveButton.textContent = panel.dataset.editSaving;
        try {
            const response = await fetch(`/comments/${editForm.dataset.id}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    Accept: 'application/json',
                },
                body: JSON.stringify({ comment: input.value }),
            });
            const payload = response.headers.get('content-type')?.includes('application/json')
                ? await response.json()
                : null;

            if (response.status === 422) {
                error.textContent = payload?.errors?.comment?.[0] || panel.dataset.editError;
                error.classList.remove('hidden');
                return;
            }
            if (!response.ok || !payload?.comment) throw new Error(panel.dataset.editError);

            const item = editForm.closest('.comment-card');
            item.querySelector('.comment-body').textContent = payload.comment.body;
            input.value = payload.comment.body;
            editForm.classList.add('hidden');
            item.querySelector('.comment-content').classList.remove('hidden');
            item.querySelector('.edit-comment').focus();
            showFeedback(panel.dataset.editSaved);
        } catch {
            error.textContent = panel.dataset.editError;
            error.classList.remove('hidden');
        } finally {
            saveButton.disabled = false;
            cancelButton.disabled = false;
            saveButton.removeAttribute('aria-busy');
            saveButton.textContent = panel.dataset.editSave;
        }
    });
}

const deleteDialog = document.getElementById('comment-delete-dialog');

if (comments && count && deleteDialog) {
    const confirmButton = document.getElementById('comment-delete-confirm');
    const cancelButton = document.getElementById('comment-delete-cancel');
    const confirmLabel = document.getElementById('comment-delete-label');
    const deleteSpinner = document.getElementById('comment-delete-spinner');
    const deleteError = document.getElementById('comment-delete-error');
    let pendingCommentId = null;

    comments.addEventListener('click', (event) => {
        const button = event.target.closest?.('.delete-comment');
        if (!button || confirmButton.disabled) return;

        pendingCommentId = button.dataset.id;
        deleteError.textContent = '';
        deleteError.classList.add('hidden');
        deleteDialog.showModal();
    });

    cancelButton.addEventListener('click', () => deleteDialog.close());
    deleteDialog.addEventListener('close', () => { pendingCommentId = null; });
    deleteDialog.addEventListener('cancel', (event) => {
        if (confirmButton.disabled) event.preventDefault();
    });

    confirmButton.addEventListener('click', async () => {
        if (!pendingCommentId || confirmButton.disabled) return;
        const commentId = pendingCommentId;
        const loadedCount = comments.children.length;

        confirmButton.disabled = true;
        cancelButton.disabled = true;
        confirmButton.setAttribute('aria-busy', 'true');
        deleteSpinner.classList.remove('hidden');
        confirmLabel.textContent = deleteDialog.dataset.deleting;
        deleteError.textContent = '';
        deleteError.classList.add('hidden');
        let deletionErrorMessage = deleteDialog.dataset.error;
        let deletionSucceeded = false;

        try {
            const response = await fetch(`/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    Accept: 'application/json',
                },
            });
            const payload = response.headers.get('content-type')?.includes('application/json')
                ? await response.json()
                : null;

            if (response.redirected || !response.ok || !payload) {
                deletionErrorMessage = response.redirected ? deleteDialog.dataset.sessionError : deleteDialog.dataset.error;
                throw new Error(deletionErrorMessage);
            }

            updateCommentCount(Math.max(0, Number(count.textContent) - 1));
            deletionSucceeded = true;
            deleteDialog.close();
            showFeedback(payload.message || deleteDialog.dataset.success);
            await refreshComments(Math.min(loadedCount, Number(count.textContent)));
            sortSelect?.focus();
        } catch {
            if (deletionSucceeded) {
                document.getElementById(`comment-${commentId}`)?.remove();
                loadMoreButton.dataset.offset = String(comments.children.length);
                loadMoreButton.dataset.hasMore = String(comments.children.length < Number(count.textContent));
                loadMoreButton.classList.toggle('hidden', loadMoreButton.dataset.hasMore !== 'true');
                showListError(panel.dataset.sortError);
            } else {
                deleteError.textContent = deletionErrorMessage;
                deleteError.classList.remove('hidden');
            }
        } finally {
            confirmButton.disabled = false;
            cancelButton.disabled = false;
            confirmButton.removeAttribute('aria-busy');
            deleteSpinner.classList.add('hidden');
            confirmLabel.textContent = deleteDialog.dataset.deleteLabel;
        }
    });
}
