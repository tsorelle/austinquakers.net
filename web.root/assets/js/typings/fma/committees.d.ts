/**
 * Created by Terry on 5/19/2016.
 */
interface IFmaCommitteeUpdate {
    committeeId: any;
    name : string;
    mailbox : string;
    active : boolean;
    isStanding : boolean;
    isLiaison : boolean;
    membershipRequired : boolean;
    description : string;
    notes : string;
}

interface IFmaCommittee extends IFmaCommitteeUpdate {
    dateAdded : string;
    dateUpdated : string;
}

interface ITermOfService {
    personId : any;
    committeeId: any;
    committeeMemberId: any;
    statusId: any;
    startOfService: string;
    endOfService: string;
    dateRelieved: string;
    roleId: any;
    notes: string;
}

interface ITermOfServiceListItem extends ITermOfService {
    name: string;
    email: string;
    phone: string;
    role: string;
    termOfService: string;
    dateAdded : string;
    dateUpdated : string;
}

interface IGetCommitteeResponse {
    committee: IFmaCommittee;
    members : ITermOfServiceListItem[];
}
