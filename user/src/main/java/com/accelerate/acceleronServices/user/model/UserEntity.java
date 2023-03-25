package com.accelerate.acceleronServices.user.model;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import javax.persistence.*;
import javax.validation.constraints.NotNull;

@Entity
@Data
@AllArgsConstructor
@NoArgsConstructor
@Table(name="z_user")
public class UserEntity {
    @Id
    @NotNull
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    int id;

    @NotNull
    @Column(name = "user_id")
    String userId;

    @NotNull
    @Column(name = "userName")
    String userName;


    @Column(name = "userEmail")
    String userEmail;
}
