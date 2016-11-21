from sqlalchemy import create_engine, Column, ForeignKey
from sqlalchemy import String, DateTime, Boolean, DECIMAL
from sqlalchemy.dialects.mysql import INTEGER
from sqlalchemy.orm import sessionmaker, scoped_session, relationship
from sqlalchemy.ext.declarative import declarative_base

import random
import datetime
import time
import hashlib
import string

from dbconfig import *

Base = declarative_base()

class Basic():
    def set(self, Column, value):
        if hasattr(self,Column):
            setattr(self, Column, value)
    def get(self, Column):
        if hasattr(self, Column):
            return getattr(self, Column)

#A table that stores information about every user using the app.
class User(Base, Basic):
    __tablename__ = 'user'
    id = Column(INTEGER(unsigned=True), primary_key=True, autoincrement=True)
    mail = Column(String(255), nullable=False)
    password = Column(String(128))
    salt = Column(String(8))
    name = Column(String(20))
    age = Column(INTEGER(3))
    avatar = Column(String(255))
    signupat = Column(DateTime, default=datetime.datetime.utcnow, nullable=False)
    lastlogin = Column(DateTime, default=datetime.datetime.utcnow, nullable=False)

    def setPassword(self, password):
        salt_list = string.digits+string.ascii_uppercase+string.ascii_lowercase
        self.salt = ''.join(random.choice(salt_list) for i in xrange(8))
        self.password = '%s' % (hashlib.sha512(str(password)+salt).hexdigest())
    def checkPassword(self, password):
        return True if self.password == '%s' % (hashlib.sha512(str(password)+salt).hexdigest()) else False
    def __repr__(self):
        return '<User %r>' % self.name

class UserInfo(Base, Basic):
    __tablename__ = 'user_info'
    id = Column(INTEGER(unsigned=True), primary_key=True, autoincrement=True)
    userid = Column(INTEGER(unsigned=True), ForeignKey('user.id'), nullable=False)
    user = relationship('User',backref='user_info')
    weight = Column(DECIMAL(4,1))
    sportiness = Column(INTEGER(3))
    updatetime = Column(DateTime, default=datetime.datetime.utcnow, nullable=False)
    def __repr__(self):
        return '<UserInfo %r>' % self.name
#
class Device(Base, Basic):
    __tablename__ = 'device'
    id = Column(INTEGER(unsigned=True), primary_key=True, autoincrement=True)
    deviceid = Column(String(255),nullable=True)
    userid = Column(INTEGER(unsigned=True), ForeignKey('user.id'), nullable=False)
    user = relationship('User',backref='device')
#A table that stores auth sessions and their assocations to the user.
class Auth(Base, Basic):
    __tablename__ = 'auth'
    id = Column(INTEGER(unsigned=True), primary_key=True, autoincrement=True)
    userid = Column(INTEGER(unsigned=True), ForeignKey('user.id'), nullable=False)
    user = relationship('User',backref='auth')
    token = Column(String(128), nullable=False)
    created = Column(DateTime, default=datetime.datetime.utcnow, nullable=False)
    isdestroy = Column(INTEGER(1, unsigned=True), default=0, nullable=False)
    def generateToken(self, password = "h4pp3rm373r"):
        plaintext = str(time.time()) + str(self.id) + str(password)
        self.token = '%s' % (hashlib.md5(plaintext).hexdigest().upper())
    def checkToken(self, token):
        return True if self.token == token else False
    def __repr__(self):
        return '<Auth %r>' % self.id

#A table that stores all happiness data associated to an user.
class HappinessData(Base, Basic):
    __tablename__ = 'happiness_data'
    id = Column(INTEGER(unsigned=True), primary_key=True, autoincrement=True)
    userid = Column(INTEGER(unsigned=True), ForeignKey('user.id'), nullable=False)
    user = relationship('User',backref='happiness_data')
    timestamp = Column(DateTime, default=datetime.datetime.utcnow)
    happiness = Column(INTEGER(unsigned=True))
    whohaveyoubeenwith = Column(INTEGER(unsigned=True))
    didyoudosports = Column(Boolean)
    def __init__(self, userid):
        self.userid = userid
    def __repr__(self):
        return '<%d\'s Happiness Data %d>' % (self.userid, self.id)


#A table that stores all measuring data associated to an user.
class SensorData(Base, Basic):
    __tablename__ = 'sensor_data'
    id = Column(INTEGER(unsigned=True), primary_key=True, autoincrement=True)
    userid = Column(INTEGER(unsigned=True), ForeignKey('user.id'), nullable=False)
    user = relationship('User',backref='sensor_data')
    timestamp = Column(DateTime, default=datetime.datetime.utcnow)
    steps = Column(INTEGER(unsigned=True))
    avgbpm = Column(INTEGER(unsigned=True))
    minbpm = Column(INTEGER(unsigned=True))
    maxbpm = Column(INTEGER(unsigned=True))
    avglightlevel = Column(INTEGER(unsigned=True))
    activity = Column(INTEGER(unsigned=True))
    sleepseconds = Column(INTEGER(unsigned=True))
    positionlat = Column(DECIMAL(9,6))
    positionlon = Column(DECIMAL(9,6))
    altitude = Column(DECIMAL(12,6))
    acc_x = Column(INTEGER())
    acc_y = Column(INTEGER())
    acc_z = Column(INTEGER())

    def __init__(self, userid):
        self.userid = userid
    def __repr__(self):
        return '<%d\'s Sensor Data %d>' % (self.userid, self.id)

if __name__ == '__main__':
    engine = create_engine(connect_info)
    db = scoped_session(sessionmaker(bind=engine))
    metadata = Base.metadata
    metadata.create_all(engine)
