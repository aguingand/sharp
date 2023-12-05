import {
    CommandData,
    EntityStateValueData,
    ShowData,
    ShowFieldData,
    ShowFieldType,
    ShowLayoutSectionData,
    ShowListFieldData,
    ShowTextFieldData
} from "@/types";
import { getAppendableUri, route } from "@/utils/url";


export class Show implements ShowData {
    authorizations: ShowData['authorizations'];
    config: ShowData['config'];
    data: ShowData['data'];
    fields: ShowData['fields'];
    layout: ShowData['layout'];
    locales: ShowData['locales'];
    pageAlert: ShowData['pageAlert'];

    entityKey: string;
    instanceId?: string;

    constructor(data: ShowData, entityKey: string, instanceId?: string) {
        Object.assign(this, data);
        this.entityKey = entityKey;
        this.instanceId = instanceId;
    }

    get formUrl(): string {
        const multiformKey = this.config.multiformAttribute
            ? this.data[this.config.multiformAttribute]
            : null;

        if(route().params.instanceId) {
            return route('code16.sharp.form.edit', {
                uri: getAppendableUri(),
                entityKey: multiformKey ? `${this.entityKey}:${multiformKey}` : this.entityKey,
                instanceId: this.instanceId,
            });
        }

        return route('code16.sharp.form.create', {
            uri: getAppendableUri(),
            entityKey: multiformKey ? `${this.entityKey}:${multiformKey}` : this.entityKey,
        });
    }

    get instanceState(): string | number | null {
        return this.config.state
            ? this.data[this.config.state.attribute] as any
            : null;
    }

    get instanceStateValue(): EntityStateValueData | undefined {
        return this.config.state?.values.find(item => item.value === this.instanceState);
    }

    get allowedInstanceCommands(): Array<Array<CommandData>> | undefined {
        return this.config.commands?.instance
            ?.map(group => group.filter(command => command.authorization));
    }

    getTitle(locale: string): string {
        if(!this.config.titleAttribute) {
            return null;
        }
        if((this.fields[this.config.titleAttribute] as ShowTextFieldData).localized) {
            return this.data[this.config.titleAttribute]?.[locale];
        }
        return this.data[this.config.titleAttribute] as string;
    }

    sectionFields(section: ShowLayoutSectionData): Array<ShowFieldData> {
        return section.columns
            .map((column) => column.fields.flat().map(field => this.fields[field.key]))
            .flat()
            .filter(Boolean);
    }

    sectionHasField(section: ShowLayoutSectionData, type: ShowFieldType): boolean {
        return this.sectionFields(section).some(field => field.type === type);
    }

    sectionShouldBeVisible(section: ShowLayoutSectionData, locale: string): boolean {
        return this.sectionFields(section)
            .some(field => this.fieldShouldBeVisible(field, this.data[field.key], locale));
    }

    sectionCommands(section: ShowLayoutSectionData): Array<Array<CommandData>> | null {
        if(!section.key) {
            return null;
        }
        return (this.config.commands[section.key] ?? [])
            .map(group => group.filter(command => command.authorization));
    }

    fieldShouldBeVisible(field: ShowFieldData, value: ShowFieldData['value'], locale: string): boolean {
        if(field.type === 'entityList') {
            return true;
        }

        if(field.emptyVisible) {
            return true;
        }

        if(field.type === 'text' && field.localized) {
            return !!(value as ShowTextFieldData['value'])?.[locale];
        }

        if(field.type === 'list') {
            return (value as ShowListFieldData['value'])?.length > 0;
        }

        return !!value;
    }
}
