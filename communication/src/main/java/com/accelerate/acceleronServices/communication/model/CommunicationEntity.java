package com.accelerate.acceleronServices.communication.model;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import javax.persistence.*;
import javax.validation.constraints.NotNull;

@Entity
@Data
@AllArgsConstructor
@NoArgsConstructor
@Table(name="z_communication")
public class CommunicationEntity {
    @Id
    @NotNull
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    int id;

    @NotNull
    @Column(name = "userId")
    String mobileNo;

    @NotNull
    @Column(name = "userName")
    String userName;


    @Column(name = "userEmail")
    String userEmail;
}
