package com.accelerate.acceleronServices.license.model;


import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Id;
import javax.persistence.Table;
import javax.validation.constraints.NotNull;

@Entity
@Data
@NoArgsConstructor
@AllArgsConstructor
@Table(name = "z_accelerate_license")
public class LicenseEntity {

    @Id
    @NotNull
    int id;

    @NotNull
    String license;

    @Column(name = "machineUID")
    @NotNull
    String machineUID;

    @Column(name = "dateInstall")
    @NotNull
    String dateInstall;

    @Column(name = "dateExpire")
    @NotNull
    String dateExpire;

    @Column(name = "isTrial")
    @NotNull
    boolean isTrial;

    @Column(name = "machineCustomName")
    @NotNull
    String machineCustomName;

    @NotNull
    String branch;

    @Column(name = "branchName")
    @NotNull
    String branchName;

    @NotNull
    String client;

    @Column(name = "isOnlineEnabled")
    @NotNull
    boolean isOnlineEnabled;

    @Column(name = "isActive")
    @NotNull
    boolean isActive;

    @Column(name = "isDeleted", columnDefinition = "integer default 0")
    @NotNull
    int isDeleted;
}
