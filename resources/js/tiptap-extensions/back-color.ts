/* Credit: https://gist.github.com/gregveres/973e8d545ab40dc375b47ebc63f92846 */

import { Extension } from "@tiptap/core";
import "@tiptap/extension-text-style";

export type ColorOptions = {
    types: string[];
};

declare module "@tiptap/core" {
    interface Commands<ReturnType> {
        backColor: {
            /**
             * Set the text color
             */
            setBackColor: (color: string) => ReturnType;
            /**
             * Unset the text color
             */
            unsetBackColor: () => ReturnType;
        };
    }
}

export const BackColor = Extension.create<ColorOptions>({
    name: "backColor",

    addOptions() {
        return {
            types: ["textStyle"],
        };
    },

    addGlobalAttributes() {
        return [
            {
                types: this.options.types,
                attributes: {
                    backgroundColor: {
                        default: null,
                        parseHTML: (element) =>
                            element.style.backgroundColor.replace(/['"]+/g, ""),
                        renderHTML: (attributes) => {
                            if (!attributes.backgroundColor) {
                                return {};
                            }

                            return {
                                style: `background-color: ${attributes.backgroundColor}`,
                            };
                        },
                    },
                },
            },
        ];
    },

    addCommands() {
        return {
            setBackColor:
                (color) =>
                    ({ chain }) => {
                        return chain().setMark("textStyle", { backgroundColor: color }).run();
                    },
            unsetBackColor:
                () =>
                    ({ chain }) => {
                        return chain()
                            .setMark("textStyle", { backgroundColor: null })
                            .removeEmptyTextStyle()
                            .run();
                    },
        };
    },
});
