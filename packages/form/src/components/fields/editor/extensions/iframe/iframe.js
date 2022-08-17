import { Node } from '@tiptap/core'
import { PasteRule } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-2";
import IframeNode from "./IframeNode";

export const Iframe =  Node.create({
    name: 'iframe',

    group: 'block',

    atom: true,

    addOptions: () => ({
        allowFullscreen: false,
        HTMLAttributes: {
            class: 'iframe-wrapper',
        },
    }),

    addAttributes() {
        return {
            src: {
                default: null,
            },
            frameborder: {
                default: 0,
            },
            width: {
                default: null
            },
            height: {
                default: null
            },
            allow: {
                default: null,
            },
            allowfullscreen: {
                default: this.options.allowFullscreen,
                parseHTML: () => this.options.allowFullscreen,
            },
            isNew: {
                default: false,
                renderHTML: () => null,
            },
        }
    },

    parseHTML() {
        return [{
            tag: 'iframe',
        }]
    },

    renderHTML({ HTMLAttributes }) {
        return ['div', this.options.HTMLAttributes, ['iframe', HTMLAttributes]]
    },

    addPasteRules() {
        return [
            new PasteRule({
                find: /(?:^|\s)(<iframe(.+)<\/iframe>).*/g,
                handler: ({ state, range, match }) => {
                    const html = match[1];
                    setTimeout(() => {
                        this.editor.commands.insertContentAt(range, html);
                    });
                }
            }),
        ]
    },

    addCommands() {
        return {
            insertIframe: () => ({ commands, tr }) => {
                return commands.insertContentAt(tr.selection.to, {
                    type: this.name,
                    attrs: {
                        isNew: true,
                    },
                });
            },
        }
    },

    addNodeView() {
        return VueNodeViewRenderer(IframeNode);
    },
});
