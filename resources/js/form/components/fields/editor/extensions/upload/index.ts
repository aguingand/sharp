import { reactive } from 'vue';
import debounce from 'lodash/debounce';
import { filesEquals } from "@/utils/upload";
import { Upload, UploadOptions } from "./upload";
import { api } from "@/api";
import { FormFieldProps } from "@/form/components/types";
import { FormEditorFieldData, FormUploadFieldValueData } from "@/types";
import { route } from "@/utils/url";

export function getUploadExtension(
    props: FormFieldProps<FormEditorFieldData>,
    { onUpdate, entityKey, instanceId }: {
        onUpdate: (files: FormUploadFieldValueData[]) => void,
        entityKey: string,
        instanceId: string|number,
    }
) {
    const state = reactive({
        registeredFiles: [],
        created: false,
        resolved: null,
        onResolve: null,
    });

    state.resolved = new Promise(resolve => state.onResolve = resolve);

    const resolveFiles = files => {
        return api.post(route('code16.sharp.api.files.show', { entityKey, instanceId }), { files })
            .then(response => response.data.files);
    }

    const config = {
        onBeforeCreate: () => {
            onUpdate([])
        },
        onCreate: debounce(async () => {
            if(state.registeredFiles.length > 0) {
                const files = await resolveFiles(state.registeredFiles);
                onUpdate(files);
                state.onResolve();
            }
            state.created = true;
            state.registeredFiles = [];
        }),
    }

    const options: UploadOptions = {
        fieldProps: props.field.embeds.upload,
        fieldErrorKey: props.fieldErrorKey,
        state,
        async registerFile(attrs) {
            if(state.created) {
                onUpdate([
                    ...props.value.files,
                    attrs,
                ]);
                return attrs;
            }

            state.registeredFiles.push(attrs);
            await state.resolved;
            return props.value.files.find(file => filesEquals(attrs, file));
        },
        onSuccess(uploadedFile: FormUploadFieldValueData) {
            onUpdate([
                ...props.value.files,
                uploadedFile,
            ]);
        },
        onRemove(removedFile: FormUploadFieldValueData) {
            onUpdate(
                props.value.files
                    .filter(file => !filesEquals(file, removedFile)),
            );
        },
        onUpdate(updatedFile: FormUploadFieldValueData) {
            onUpdate(
                props.value.files
                    .map(file => filesEquals(file, updatedFile) ? updatedFile : file),
            );
        }
    };

    return Upload
        .extend(config)
        .configure(options);
}
