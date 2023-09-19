export type BreadcrumbData = {
  items: Array<BreadcrumbItemData>;
};
export type BreadcrumbItemData = {
  type: string;
  label: string;
  documentTitleLabel: string | null;
  entityKey: string;
  url: string;
};
export type CheckFilterData = {
  value?: boolean | null;
  key: string;
  label: string;
  type: "check";
  default: boolean | null;
};
export type CommandAction =
  | "download"
  | "info"
  | "link"
  | "reload"
  | "refresh"
  | "step"
  | "streamDownload"
  | "view";
export type CommandData = {
  key: string;
  label: string | null;
  description: string | null;
  type: CommandType;
  confirmation: string | null;
  modal_title: string | null;
  modal_confirm_label: string | null;
  has_form: boolean;
  authorization: Array<string | number> | boolean;
  instance_selection: InstanceSelectionMode | null;
  primary: boolean | null;
};
export type CommandFormData = {
  data: { [key: string]: FormFieldData["value"] };
  fields: { [key: string]: FormFieldData } | null;
  layout: FormLayoutData | null;
  locales: Array<string> | null;
  pageAlert: PageAlertData | null;
};
export type CommandReturnData =
  | { action: "link"; link: string }
  | { action: "info"; message: string }
  | { action: "refresh"; items?: Array<number | string> }
  | { action: "reload" }
  | { action: "step"; step: string }
  | { action: "view"; html: string };
export type CommandType = "dashboard" | "entity" | "instance";
export type ConfigCommandsData = Record<
  CommandType | string,
  Array<Array<CommandData>>
>;
export type ConfigFiltersData = { _root: Array<FilterData> } & {
  [key: string]: Array<FilterData>;
};
export type DashboardConfigData = {
  commands: ConfigCommandsData | null;
  filters: ConfigFiltersData | null;
};
export type DashboardData = {
  widgets: Array<WidgetData>;
  config: DashboardConfigData;
  layout: DashboardLayoutData;
  data: { [key: string]: any };
  pageAlert: PageAlertData | null;
};
export type DashboardLayoutData = {
  sections: Array<DashboardLayoutSectionData>;
};
export type DashboardLayoutSectionData = {
  key: string | null;
  title: string;
  rows: Array<Array<DashboardLayoutWidgetData>>;
};
export type DashboardLayoutWidgetData = {
  size: number;
  key: string;
};
export type DateRangeFilterData = {
  value?: DateRangeFilterValueData | null;
  key: string;
  label: string;
  type: "daterange";
  default: DateRangeFilterValueData | null;
  required: boolean;
  mondayFirst: boolean;
  displayFormat: string;
};
export type DateRangeFilterValueData = {
  start: string;
  end: string;
};
export type EmbedData = {
  key: string;
  label: string;
  tag: string;
  attributes: Array<string>;
  template: string;
};
export type EmbedFormData = {
  data: { [key: string]: FormFieldData["value"] };
  fields: { [key: string]: FormFieldData };
  layout: FormLayoutData;
};
export type EntityAuthorizationsData = {
  view: Array<number | string>;
  update: Array<number | string>;
  delete: Array<number | string>;
  create: boolean;
};
export type EntityListConfigData = {
  instanceIdAttribute: string;
  searchable: boolean;
  reorderable: boolean;
  defaultSort: string | null;
  defaultSortDir: string | null;
  hasShowPage: boolean;
  deleteConfirmationText: string;
  deleteHidden: boolean;
  multiformAttribute: string | null;
  filters: ConfigFiltersData | null;
  commands: ConfigCommandsData | null;
  state: EntityStateData | null;
};
export type EntityListData = {
  authorizations: EntityAuthorizationsData;
  config: EntityListConfigData;
  fields: Array<EntityListFieldData>;
  data: Array<{ [key: string]: any }>;
  forms: { [key: string]: EntityListMultiformData };
  meta: PaginatorMetaData | null;
  pageAlert: PageAlertData | null;
};
export type EntityListFieldData = {
  key: string;
  label: string;
  sortable: boolean;
  html: boolean;
  size: string;
  hideOnXS: boolean;
  sizeXS: string;
};
export type EntityListMultiformData = {
  key: string;
  label: string;
  instances: Array<number | string>;
};
export type EntityListQueryParamsData = {
  search?: string | null;
  page?: number | null;
  sort?: string | null;
  dir?: "asc" | "desc";
};
export type EntityStateData = {
  attribute: string;
  values: Array<EntityStateValueData>;
  authorization: boolean | Array<string | number> | null;
};
export type EntityStateValueData = {
  value: string | number;
  label: string;
  color: string;
};
export type FigureWidgetData = {
  value?: {
    key: string;
    data: {
      figure: string;
      unit: string;
      evolution: { increase: boolean; value: string };
    };
  };
  key: string;
  type: "figure";
  title: string | null;
  link: string | null;
};
export type FilterData =
  | CheckFilterData
  | DateRangeFilterData
  | SelectFilterData;
export type FilterType = "select" | "daterange" | "check";
export type FormAutocompleteFieldData = {
  value: string | number | null | { [locale: string]: string | number | null };
  key: string;
  type: "autocomplete";
  mode: "local" | "remote";
  itemIdAttribute: string;
  listItemTemplate: string;
  resultItemTemplate: string;
  searchMinChars: number;
  localValues: Array<{ [key: string]: any }> | FormDynamicOptionsData;
  debounceDelay: number;
  dataWrapper: string;
  placeholder: string | null;
  templateData: { [key: string]: any } | null;
  searchKeys: Array<string> | null;
  remoteEndpoint: string | null;
  remoteMethod: "GET" | "POST" | null;
  remoteSearchAttribute: string | null;
  localized: boolean | null;
  dynamicAttributes: Array<FormDynamicAttributeData> | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormCheckFieldData = {
  value?: boolean;
  key: string;
  type: "check";
  text: string;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormConditionalDisplayData = {
  operator: "and" | "or";
  fields: Array<{ key: string; values: string | boolean | Array<string> }>;
};
export type FormConfigData = {
  hasShowPage: boolean;
  deleteConfirmationText: string | null;
  isSingle: boolean;
  breadcrumbAttribute: string | null;
};
export type FormData = {
  authorizations: InstanceAuthorizationsData;
  config: FormConfigData;
  data: { [key: string]: FormFieldData["value"] };
  fields: { [key: string]: FormFieldData };
  layout: FormLayoutData;
  locales: Array<string>;
  pageAlert: PageAlertData | null;
};
export type FormDateFieldData = {
  value: string | null;
  key: string;
  type: "date";
  hasDate: boolean;
  hasTime: boolean;
  minTime: string;
  maxTime: string;
  stepTime: number;
  mondayFirst: boolean;
  displayFormat: string;
  language: string;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormDynamicAttributeData = {
  name: string;
  type: "map" | "template";
  path: Array<string> | null;
  default: string | null;
};
export type FormDynamicOptionsData = {
  [key: string]:
    | FormDynamicOptionsData
    | Array<{ id: string | number; label: string }>;
};
export type FormEditorFieldData = {
  value: string | null | { [locale: string]: string | null };
  key: string;
  type: "editor";
  minHeight: number;
  markdown: boolean;
  inline: boolean;
  showCharacterCount: boolean;
  embeds: { upload: FormEditorFieldUploadData } & {
    [key: string]: FormEditorFieldEmbedData;
  };
  toolbar: Array<FormEditorToolbarButton>;
  maxHeight: number | null;
  maxLength: number | null;
  placeholder: string | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
  localized: boolean | null;
};
export type FormEditorFieldEmbedData = {
  key: string;
  label: string;
  tag: string;
  attributes: Array<string>;
  template: string;
};
export type FormEditorFieldUploadData = {
  transformable: boolean;
  transformKeepOriginal: boolean | null;
  transformableFileTypes: Array<string> | null;
  ratioX: number | null;
  ratioY: number | null;
  maxFileSize: number | null;
  fileFilter: Array<any> | string | null;
};
export type FormEditorToolbarButton =
  | "bold"
  | "italic"
  | "highlight"
  | "small"
  | "bullet-list"
  | "ordered-list"
  | "link"
  | "heading-1"
  | "heading-2"
  | "heading-3"
  | "code"
  | "blockquote"
  | "upload-image"
  | "upload"
  | "horizontal-rule"
  | "table"
  | "iframe"
  | "html"
  | "code-block"
  | "superscript"
  | "undo"
  | "redo"
  | "|";
export type FormFieldData =
  | FormAutocompleteFieldData
  | FormCheckFieldData
  | FormDateFieldData
  | FormEditorFieldData
  | FormGeolocationFieldData
  | FormHtmlFieldData
  | FormListFieldData
  | FormNumberFieldData
  | FormSelectFieldData
  | FormTagsFieldData
  | FormTextFieldData
  | FormTextareaFieldData
  | FormUploadFieldData;
export type FormFieldType =
  | "autocomplete"
  | "check"
  | "date"
  | "editor"
  | "geolocation"
  | "html"
  | "list"
  | "number"
  | "select"
  | "tags"
  | "text"
  | "textarea"
  | "upload";
export type FormGeolocationFieldData = {
  value: { lng: number; lat: number };
  key: string;
  type: "geolocation";
  geocoding: boolean;
  displayUnit: "DD" | "DMS";
  zoomLevel: number;
  mapsProvider: { name: "osm" | "gmaps"; options: { apiKey: string } };
  geocodingProvider: { name: "osm" | "gmaps"; options: { apiKey: string } };
  initialPosition: { lng: number; lat: number };
  boundaries: {
    ne: { lat: number; lng: number };
    sw: { lat: number; lng: number };
  };
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormHtmlFieldData = {
  value: { [key: string]: any } | null;
  key: string;
  type: "html";
  template: string;
  templateData: { [key: string]: any } | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormLayoutColumnData = {
  size: number;
  fields: Array<Array<LayoutFieldData>>;
};
export type FormLayoutData = {
  tabbed: boolean;
  tabs: Array<FormLayoutTabData>;
};
export type FormLayoutFieldsetData = {
  legend: string;
  fields: Array<Array<any>>;
};
export type FormLayoutTabData = {
  title: string;
  columns: Array<FormLayoutColumnData>;
};
export type FormListFieldData = {
  value?: Array<{ [key: string]: any }> | null;
  key: string;
  type: "list";
  addable: boolean;
  removable: boolean;
  sortable: boolean;
  itemIdAttribute: string;
  itemFields: { [key: string]: FormFieldData };
  addText: string;
  collapsedItemTemplate: string | null;
  maxItemCount: number | null;
  bulkUploadField: string | null;
  bulkUploadLimit: number | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormNumberFieldData = {
  value: number | null;
  key: string;
  type: "number";
  step: number;
  showControls: boolean;
  min: number | null;
  max: number | null;
  placeholder: string | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormSelectFieldData = {
  value: string | number | Array<string | number> | null;
  key: string;
  type: "select";
  options:
    | Array<{ id: string | number; label: string }>
    | FormDynamicOptionsData;
  multiple: boolean;
  showSelectAll: boolean;
  clearable: boolean;
  display: "list" | "dropdown";
  inline: boolean;
  dynamicAttributes: Array<FormDynamicAttributeData> | null;
  maxSelected: number | null;
  localized: boolean | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormTagsFieldData = {
  value: Array<{ id: string | number; label: string }> | null;
  key: string;
  type: "tags";
  creatable: boolean;
  createText: string;
  options: Array<{ id: string | number; label: string }>;
  maxTagCount: number | null;
  localized: boolean | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormTextFieldData = {
  value: string | null | { [locale: string]: string | null };
  key: string;
  type: "text";
  inputType: "text" | "password";
  placeholder: string | null;
  maxLength: number | null;
  localized: boolean | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormTextareaFieldData = {
  value: string | null | { [locale: string]: string | null };
  key: string;
  type: "textarea";
  rows: number | null;
  placeholder: string | null;
  maxLength: number | null;
  localized: boolean | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type FormUploadFieldData = {
  value: {
    name: string;
    disk: string;
    path: string;
    uploaded?: boolean;
    transformed?: boolean;
    filters?: {
      crop: { width: number; height: number; x: number; y: number };
      rotate: { angle: number };
    };
  } | null;
  key: string;
  type: "upload";
  transformable: boolean;
  compactThumbnail: boolean;
  transformKeepOriginal: boolean | null;
  transformableFileTypes: Array<string> | null;
  ratioX: number | null;
  ratioY: number | null;
  maxFileSize: number | null;
  fileFilter: Array<any> | string | null;
  label: string | null;
  readOnly: boolean | null;
  conditionalDisplay: FormConditionalDisplayData | null;
  helpMessage: string | null;
  extraStyle: string | null;
};
export type GlobalFiltersData = {
  filters: ConfigFiltersData;
};
export type GraphWidgetData = {
  value?: {
    key: string;
    datasets: Array<{ label: string; data: number[]; color: string }>;
    labels: string[];
  };
  key: string;
  type: "graph";
  title: string | null;
  display: GraphWidgetDisplay;
  showLegend: boolean;
  minimal: boolean;
  ratioX: number | null;
  ratioY: number | null;
  height: number | null;
  dateLabels: boolean;
  options: { curved: boolean; horizontal: boolean };
};
export type GraphWidgetDisplay = "bar" | "line" | "pie";
export type InstanceAuthorizationsData = {
  view: boolean;
  create: boolean;
  update: boolean;
  delete: boolean;
};
export type InstanceSelectionMode = "required" | "allowed";
export type LayoutFieldData = {
  key: string;
  size: number;
  sizeXS: number;
  item: { [key: string]: any } | null;
};
export type MenuData = {
  items: Array<MenuItemData>;
  userMenu: UserMenuData;
};
export type MenuItemData = {
  icon: string | null;
  label: string | null;
  url: string | null;
  isExternalLink: boolean;
  entityKey: string | null;
  isSeparator: boolean;
  current: boolean;
  children: Array<MenuItemData> | null;
  isCollapsible: boolean;
};
export type NotificationData = {
  title: string;
  level: NotificationLevel;
  message: string | null;
  autoHide: boolean;
};
export type NotificationLevel = "info" | "success" | "warning" | "danger";
export type OrderedListWidgetData = {
  value?: {
    key: string;
    data: Array<{ label: string; url?: string; count?: number }>;
  };
  key: string;
  type: "list";
  title: string | null;
  link: string | null;
  html: boolean;
};
export type PageAlertData = {
  level: PageAlertLevel;
  text: string;
};
export type PageAlertLevel =
  | "danger"
  | "info"
  | "primary"
  | "secondary"
  | "warning";
export type PaginatorMetaData = {
  current_page: number;
  first_page_url: string;
  from: number;
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number;
  links: Array<{ url: string | null; label: string; active: boolean }>;
  last_page: number | null;
  last_page_url: string | null;
  total: number | null;
};
export type PanelWidgetData = {
  value?: { key: string; data: { [key: string]: any } };
  key: string;
  type: "panel";
  template: string;
  title: string | null;
  link: string | null;
};
export type SelectFilterData = {
  value?: number | string | Array<number | string> | null;
  key: string;
  label: string | null;
  type: "select";
  default: number | string | Array<number | string> | null;
  multiple: boolean;
  required: boolean;
  values: Array<{ id: string | number } & { [key: string]: any }>;
  master: boolean;
  searchable: boolean;
  searchKeys: Array<any>;
  template: string;
};
export type ShowConfigData = {
  deleteConfirmationText: string;
  isSingle: boolean;
  commands: ConfigCommandsData | null;
  multiformAttribute: string | null;
  titleAttribute: string | null;
  breadcrumbAttribute: string | null;
  state: EntityStateData | null;
};
export type ShowData = {
  authorizations: InstanceAuthorizationsData;
  config: ShowConfigData;
  data: { [key: string]: ShowFieldData["value"] };
  fields: { [key: string]: ShowFieldData };
  layout: ShowLayoutData;
  locales: Array<string> | null;
  pageAlert: PageAlertData | null;
};
export type ShowEntityListFieldData = {
  value?: null | null;
  key: string;
  type: "entityList";
  emptyVisible: boolean;
  entityListKey: string;
  hiddenCommands: { instance: Array<string>; entity: Array<string> };
  showEntityState: boolean;
  showReorderButton: boolean;
  showCreateButton: boolean;
  showSearchField: boolean;
  showCount: boolean;
  label: string | null;
  hiddenFilters: { [key: string]: any } | null;
};
export type ShowFieldData =
  | ShowEntityListFieldData
  | ShowFileFieldData
  | ShowHtmlFieldData
  | ShowListFieldData
  | ShowPictureFieldData
  | ShowTextFieldData;
export type ShowFieldType =
  | "file"
  | "html"
  | "list"
  | "picture"
  | "text"
  | "entityList";
export type ShowFileFieldData = {
  value?: {
    disk: string;
    name: string;
    path: string;
    thumbnail: string;
    size: number;
  };
  key: string;
  type: "file";
  emptyVisible: boolean;
  label: string | null;
};
export type ShowHtmlFieldData = {
  value?: { [key: string]: any };
  key: string;
  type: "html";
  emptyVisible: boolean;
  template: string;
  templateData: { [key: string]: any } | null;
};
export type ShowLayoutColumnData = {
  size: number;
  fields: Array<Array<LayoutFieldData>>;
};
export type ShowLayoutData = {
  sections: Array<ShowLayoutSectionData>;
};
export type ShowLayoutSectionData = {
  key: string | null;
  title: string;
  collapsable: boolean;
  columns: Array<ShowLayoutColumnData>;
};
export type ShowListFieldData = {
  value?: Array<{ [key: string]: ShowFieldData["value"] }>;
  key: string;
  type: "list";
  emptyVisible: boolean;
  label: string | null;
  itemFields: { [key: string]: ShowFieldData };
};
export type ShowPictureFieldData = {
  value?: string;
  key: string;
  type: "picture";
  emptyVisible: boolean;
};
export type ShowTextFieldData = {
  value?: string | { [key: string]: string };
  key: string;
  type: "text";
  emptyVisible: boolean;
  html: boolean;
  localized: boolean | null;
  collapseToWordCount: number | null;
  embeds: { [key: string]: EmbedData } | null;
  label: string | null;
};
export type UserData = {
  name: string | null;
};
export type UserMenuData = {
  items: Array<MenuItemData>;
};
export type WidgetData =
  | FigureWidgetData
  | GraphWidgetData
  | OrderedListWidgetData
  | PanelWidgetData;
export type WidgetType = "figure" | "graph" | "list" | "panel";
