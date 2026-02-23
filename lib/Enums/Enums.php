<?php
namespace RubikaPhp\Enums;

enum ChatTypeEnum: string {
    case USER = "User";
    case BOT = "Bot";
    case GROUP = "Group";
    case CHANNEL = "Channel";
}

enum MessageSenderEnum: string {
    case USER = "User";
    case BOT = "Bot";
}

enum UpdateTypeEnum: string {
    case NEW_MESSAGE = "NewMessage";
    case STARTED_BOT = "StartedBot";
    case STOPPED_BOT = "StoppedBot";
    case UPDATE_MESSAGE = "UpdatedMessage";
    case REMOVE_MESSAGE = "RemovedMessage";
    case UPDATE_PAYMENT = "UpdatedPayment";
}

enum ButtonTypeEnum: string {
    case SIMPLE = "Simple";
    case SELECTION = "Selection";
    case CALENDAR = "Calendar";
    case NUMBER_PICKER = "NumberPicker";
    case STRING_PICKER = "StringPicker";
    case LOCATION = "Location";
    case PAYMENT = "Payment";
    case CAMERA_IMAGE = "CameraImage";
    case CAMERA_VIDEO = "CameraVideo";
    case GALLERY_IMAGE = "GalleryImage";
    case GALLERY_VIDEO = "GalleryVideo";
    case FILE = "File";
    case AUDIO = "Audio";
    case RECORD_AUDIO = "RecordAudio";
    case TEXT_BOX = "TextBox";
    case LINK = "Link";
}

enum ChatKeypadTypeEnum: string {
    case DEFAULT = "None";
    case NEW = "New";
    case REMOVE = "Remove";
}

enum ForwardedFromEnum: string {
    case USER = "User";
    case CHANNEL = "Channel";
    case BOT = "Bot";
}

enum LiveLocationStatusEnum: string {
    case STOPPED = "Stopped";
    case LIVE = "Live";
}

enum PollStatusEnum: string {
    case OPEN = "Open";
    case CLOSED = "Closed";
}

enum ButtonSelectionTypeEnum: string {
    case TEXTONLY = "TextOnly";
    case TEXTIMGTHU = "TextImgThu";
    case TEXTIMGBIG = "TextImgBig";
}

enum ButtonCalendarTypeEnum: string {
    case DATEPERSIAN = "DatePersian";
    case DATEGREGORIAN = "DateGregorian";
}

enum ButtonLocationTypeEnum: string {
    case PICKER = "Picker";
    case VIEW = "View";
}

enum ButtonTextboxTypeLineEnum: string {
    case SINGLELINE = "SingleLine";
    case MULTILINE = "MultiLine";
}

enum PaymentStatusEnum: string {
    case PAID = "Paid";
    case NOT_PAID = "NotPaid";
}

enum ButtonTextboxTypeKeypadEnum: string {
    case STRING = "String";
    case NUMBER = "Number"; 
}

enum ButtonSelectionSearchEnum: string {
    case NONE = "None";
    case LOCAL = "Local";
    case API = "Api"; 
}

enum ButtonSelectionGetEnum: string {
    case LOCAL = "Local";
    case API = "Api";
}

enum UpdateEndpointTypeEnum: string {
    case RECEIVE_UPDATE = "ReceiveUpdate";
    case RECEIVE_INLINE_MESSAGE = "ReceiveInlineMessage";
    case RECEIVE_QUERY = "ReceiveQuery";
    case GET_SELECTION_ITEM = "GetSelectionItem";
    case SEARCH_SELECTION_ITEMS = "SearchSelectionItems";
}