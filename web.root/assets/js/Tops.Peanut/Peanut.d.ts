declare module Tops {
    export interface IPeanutClient {
        showServiceMessages(messages:IServiceMessage[]): void;
        hideServiceMessages(): void;
        showError(errorMessage:string): void;
        showMessage(messageText:string): void;
        showWarning(messageText:string): void;
        setMessageHandler(handler: IMessageHandler, successFunction?: () => void);
        startup(applicationPath: string, messageHandlerType: string, successFunction?: () => void);
        initialize(applicationPath: string, successFunction?: () => void);
        showWaiter(message:string) : void;
        hideWaiter() : void;
        showProgress(message: string) : void;
        setProgress(count: number) : void;
        loadComponent(name: string, successFunction?: () => void);
        loadCSS(name: string, successFunction?: () => void);
        loadJS(names: any, successFunction?: () => void);
        loadResources(names: any, successFunction?: () => void);
        getHtmlTemplate(name: string, successFunction: (htmlSource: string) => void);
        registerComponent(name: string, vm: any, successFunction?: () => void);

        setPageTitle(title: string) : void;
        setPageHeading(text: string) : void;
        hidePageHeading() : void;
        
        peanut: any; // Tops.Peanut;
        viewModel: any;
        serviceUrl: string;
        applicationPath: string;
    }

    export interface IMessageManager {
        addMessage : (message:string, messageType:number) => void;
        setMessage  : (message:string, messageType:number) => void;
        clearMessages : (messageType:number)=> void;
        setServiceMessages : (messages:Tops.IServiceMessage[]) => void;
    }

    export interface IMessageHandler {
        initialize(application : IPeanutClient, successFunction?: () => void): void;
        showWaiter(message) : void;
        hideWaiter() : void;
        showWaiter(message) : void;
        showServiceMessages(messages: Tops.IServiceMessage[]): void;
        hideServiceMessages(): void;
        showMessage(messageText: string): void;
        showError(errorMessage: string): void;
        showWarning(messageText: string): void;
        setErrorMessage(messageText: string): void;
        setInfoMessage(messageText: string): void;
        setWarningMessage(messageText: string): void;
        setProgress(count: number) : void;
        showProgress(message: string) : void;
        setProgress(count: number) : void;
    }

    export interface IMainViewModel {
        init(applicationPath: string, successFunction?: () => void);
    }

    export interface IServiceMessage {
        MessageType: number;
        Text: string;
    }

    export interface IServiceResponse {
        Messages: IServiceMessage[];
        Result: number;
        Value: any;
        Data: any;
    }

    export interface INameValuePair {
        Name: string;
        Value: string;
    }

    export interface IKeyValuePair {
        Key: any;
        Value: any;
    }

    export interface ILookupItem {
        Key: any;
        Text: string;
        Description: string;
    }

    export interface IListItem {
        Text: string;
        Value: any;
        Description: string;
    }

    export interface IIndexedItem extends IListItem {
        Key: any;
    }

    export interface IInputItem extends IListItem {
        Value: any;
        ErrorMessage: string;
    }

    export interface IIndexedInput extends IInputItem {
        Key: any;
    }

    export interface IEventSubscriber {
        handleEvent : (eventName: string, data?: any) => void;
    }
    
}