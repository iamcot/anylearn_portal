import { IConfiguration } from "../typings";

export const defaultConfiguration: IConfiguration = {
    chatServer: '/bot',
    frameEndpoint: '/bot/chat',
    timeFormat: 'HH:MM',
    dateTimeFormat: 'HH:MM m/d/yy',
    title: 'Trợ lí ảo anyLEARN',
    cookieValidInDays: 1,
    introMessage: 'Xin chào, mình là  trợ lý ảo anyLEARN - chuyên viên chăm sóc khách hàng tự động của anyLEARN, rất vui được hỗ trợ bạn. Mình có thể giúp gì cho bạn hôm nay?',
    greetingCard: '',
    placeholderText: 'Gửi tin nhắn ...',
    displayMessageTime: true,
    sendWidgetOpenedEvent: false,
    widgetOpenedEventData: '',
    mainColor: 'green',
    headerTextColor: 'white',
    bubbleBackground: '#408591',
    bubbleAvatarUrl: '/cdn/img/logo.png',
    desktopHeight: 450,
    desktopWidth: 370,
    mobileHeight: '100%',
    mobileWidth: '300px',
    videoHeight: 160,
    aboutLink: 'https://anylearn.vn',
    aboutText: '',
    chatId: '',
    userId: '',
    alwaysUseFloatingButton: true,
    useEcho: false,
    echoChannel: (userId: string) => '',
    echoConfiguration: {},
    echoEventName: '.message.created',
    echoChannelType: 'private'
};
