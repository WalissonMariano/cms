import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'
import Image from '@tiptap/extension-image'
import Placeholder from '@tiptap/extension-placeholder'

const root = document.querySelector('[data-post-editor]')
const input = document.querySelector('#content')

if (root && input) {
    const editor = new Editor({
        element: root,
        extensions: [
            StarterKit,
            Link.configure({ openOnClick: false }),
            Image,
            Placeholder.configure({ placeholder: 'Escreva o conteúdo do post...' }),
        ],
        content: input.value,
        onUpdate: ({ editor: current }) => {
            input.value = current.getHTML()
        },
    })

    const actions = {
        bold: () => editor.chain().focus().toggleBold().run(),
        italic: () => editor.chain().focus().toggleItalic().run(),
        heading: () => editor.chain().focus().toggleHeading({ level: 2 }).run(),
        bullet: () => editor.chain().focus().toggleBulletList().run(),
        ordered: () => editor.chain().focus().toggleOrderedList().run(),
        link: () => {
            const href = window.prompt('Endereço do link')
            if (href) {
                editor.chain().focus().setLink({ href }).run()
            }
        },
    }

    document.querySelectorAll('[data-editor-action]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault()
            actions[button.dataset.editorAction]?.()
        })
    })

    document.querySelectorAll('[data-insert-image]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault()
            editor.chain().focus().setImage({ src: button.dataset.insertImage }).run()
        })
    })
}
